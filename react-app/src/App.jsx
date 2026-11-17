import React, { useEffect, useState } from 'react';

const API_URL = 'http://127.0.0.1:8000/api/product';

async function request(url, options) {
  const headers = { ...options?.headers };
  if (options?.body) headers['Content-Type'] = 'application/json';

  const response = await fetch(url, {
    ...options,
    headers,
  });

  if (!response.ok) {
    const result = await response.json().catch(() => ({}));
    throw new Error(result.message || `Request failed (${response.status})`);
  }

  return response.status === 204 ? null : response.json();
}

export default function App() {
  const [products, setProducts] = useState([]);
  const [product, setProduct] = useState(null);
  const [page, setPage] = useState('list');
  const [loading, setLoading] = useState(true);
  const [error, setError] = useState('');

  async function loadProducts() {
    setLoading(true);
    setError('');
    try {
      const result = await request(API_URL);
      if (!Array.isArray(result.data)) throw new Error('Invalid product list response');
      setProducts(result.data);
    } catch (err) {
      setError(err.message);
    } finally {
      setLoading(false);
    }
  }

  useEffect(() => {
    loadProducts();
  }, []);

  function openForm(item = null) {
    setProduct(item);
    setError('');
    setPage('form');
  }

  async function saveProduct(event) {
    event.preventDefault();
    const name = new FormData(event.currentTarget).get('name').trim();
    if (!name) return;

    try {
      setError('');
      await request(product ? `${API_URL}/${product.id}` : API_URL, {
        method: product ? 'PUT' : 'POST',
        body: JSON.stringify({ name }),
      });
      setPage('list');
      await loadProducts();
    } catch (err) {
      setError(err.message);
    }
  }

  async function deleteProduct(id) {
    if (!window.confirm('Delete this product?')) return;
    try {
      setError('');
      await request(`${API_URL}/${id}`, { method: 'DELETE' });
      await loadProducts();
    } catch (err) {
      setError(err.message);
    }
  }

  return (
    <main className="container">
      {page === 'list' ? (
        <>
          <header className="page-header">
            <div>
              <p className="eyebrow">PRODUCTS</p>
              <h1>Product list</h1>
              <p className="subtitle">Manage your products in one place.</p>
            </div>
            <button className="button primary" onClick={() => openForm()}>
              + Add new product
            </button>
          </header>

          {error && <p className="message error">{error}</p>}

          <section className="panel">
            {loading ? (
              <p className="empty">Loading products...</p>
            ) : products.length === 0 ? (
              <p className="empty">No products yet. Add your first product.</p>
            ) : (
              <div className="table-wrap">
                <table>
                  <thead>
                    <tr>
                      <th>Product name</th>
                      <th className="actions-heading">Actions</th>
                    </tr>
                  </thead>
                  <tbody>
                    {products.map((item) => (
                      <tr key={item.id}>
                        <td>{item.name}</td>
                        <td className="actions">
                          <button className="button small" onClick={() => openForm(item)}>
                            Edit
                          </button>
                          <button
                            className="button small danger"
                            onClick={() => deleteProduct(item.id)}
                          >
                            Delete
                          </button>
                        </td>
                      </tr>
                    ))}
                  </tbody>
                </table>
              </div>
            )}
          </section>
        </>
      ) : (
        <>
          <header className="page-header">
            <div>
              <p className="eyebrow">PRODUCTS</p>
              <h1>{product ? 'Edit product' : 'Add new product'}</h1>
              <p className="subtitle">Enter the product name below.</p>
            </div>
          </header>

          {error && <p className="message error">{error}</p>}

          <form className="panel form" onSubmit={saveProduct}>
            <label htmlFor="name">Product name</label>
            <input
              id="name"
              name="name"
              type="text"
              defaultValue={product?.name || ''}
              placeholder="Enter product name"
              required
              autoFocus
            />
            <div className="form-actions">
              <button type="button" className="button" onClick={() => setPage('list')}>
                Cancel
              </button>
              <button type="submit" className="button primary">
                {product ? 'Update product' : 'Save product'}
              </button>
            </div>
          </form>
        </>
      )}
    </main>
  );
}
