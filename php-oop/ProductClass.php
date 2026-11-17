<?php

declare(strict_types=1);

final class Product
{
    public function __construct(private PDO $db)
    {
    }

    public function all(): array
    {
        return $this->db->query('SELECT id, name, description, price, stock FROM products ORDER BY id DESC')
            ->fetchAll();
    }

    public function find(int $id): ?array
    {
        $statement = $this->db->prepare(
            'SELECT id, name, description, price, stock FROM products WHERE id = :id'
        );
        $statement->execute(['id' => $id]);
        $product = $statement->fetch();

        return $product ?: null;
    }

    public function findMany(array $ids): array
    {
        $ids = array_values(array_unique(array_filter(
            array_map('intval', $ids),
            static fn (int $id): bool => $id > 0
        )));
        if ($ids === []) {
            return [];
        }

        $placeholders = implode(',', array_fill(0, count($ids), '?'));
        $statement = $this->db->prepare(
            "SELECT id, name, description, price, stock FROM products WHERE id IN ($placeholders)"
        );
        $statement->execute($ids);

        $products = [];
        foreach ($statement->fetchAll() as $product) {
            $products[(int) $product['id']] = $product;
        }

        return $products;
    }

    public function create(string $name, string $description, float $price, int $stock): int
    {
        $statement = $this->db->prepare(
            'INSERT INTO products (name, description, price, stock)
             VALUES (:name, :description, :price, :stock)'
        );
        $statement->execute([
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock' => $stock,
        ]);

        return (int) $this->db->lastInsertId();
    }

    public function update(int $id, string $name, string $description, float $price, int $stock): void
    {
        $statement = $this->db->prepare(
            'UPDATE products
             SET name = :name, description = :description, price = :price, stock = :stock
             WHERE id = :id'
        );
        $statement->execute([
            'id' => $id,
            'name' => $name,
            'description' => $description,
            'price' => $price,
            'stock' => $stock,
        ]);
    }

    public function delete(int $id): void
    {
        $statement = $this->db->prepare('DELETE FROM products WHERE id = :id');
        $statement->execute(['id' => $id]);
    }

    public function placeOrder(array $customer, array $cart): int
    {
        if ($cart === []) {
            throw new DomainException('Your cart is empty.');
        }

        $this->db->beginTransaction();
        try {
            $items = [];
            $total = 0.0;
            $lockProduct = $this->db->prepare(
                'SELECT id, name, price, stock FROM products WHERE id = :id FOR UPDATE'
            );

            foreach ($cart as $id => $quantity) {
                $id = (int) $id;
                $quantity = (int) $quantity;
                if ($id < 1 || $quantity < 1) {
                    throw new DomainException('The cart contains an invalid item.');
                }

                $lockProduct->execute(['id' => $id]);
                $product = $lockProduct->fetch();
                if (!$product) {
                    throw new DomainException('A product in your cart is no longer available.');
                }
                if ($quantity > (int) $product['stock']) {
                    throw new DomainException(
                        'There is not enough stock for ' . $product['name'] . '. Please update your cart.'
                    );
                }

                $unitPrice = (float) $product['price'];
                $items[] = [
                    'id' => $id,
                    'name' => $product['name'],
                    'quantity' => $quantity,
                    'unit_price' => $unitPrice,
                ];
                $total += $unitPrice * $quantity;
            }

            $orderStatement = $this->db->prepare(
                'INSERT INTO orders (customer_name, email, address, total)
                 VALUES (:customer_name, :email, :address, :total)'
            );
            $orderStatement->execute([
                'customer_name' => $customer['name'],
                'email' => $customer['email'],
                'address' => $customer['address'],
                'total' => number_format($total, 2, '.', ''),
            ]);
            $orderId = (int) $this->db->lastInsertId();

            $itemStatement = $this->db->prepare(
                'INSERT INTO order_items (order_id, product_id, product_name, quantity, unit_price)
                 VALUES (:order_id, :product_id, :product_name, :quantity, :unit_price)'
            );
            $stockStatement = $this->db->prepare(
                'UPDATE products SET stock = stock - :quantity WHERE id = :id'
            );
            foreach ($items as $item) {
                $itemStatement->execute([
                    'order_id' => $orderId,
                    'product_id' => $item['id'],
                    'product_name' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => number_format($item['unit_price'], 2, '.', ''),
                ]);
                $stockStatement->execute([
                    'quantity' => $item['quantity'],
                    'id' => $item['id'],
                ]);
            }

            $this->db->commit();
            return $orderId;
        } catch (Throwable $exception) {
            if ($this->db->inTransaction()) {
                $this->db->rollBack();
            }
            throw $exception;
        }
    }
}
