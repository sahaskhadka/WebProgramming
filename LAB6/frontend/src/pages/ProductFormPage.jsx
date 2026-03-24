import React, { useState, useEffect } from 'react';
import { useNavigate, useParams } from 'react-router';
import { fetchProduct, createProduct, updateProduct } from '../api/products';
import { Save, ArrowLeft } from 'lucide-react';

export default function ProductFormPage() {
  const { id } = useParams();
  const navigate = useNavigate();
  const [formData, setFormData] = useState({
    name: '',
    description: '',
    price: '',
    stock: 0,
    image_url: ''
  });

  useEffect(() => {
    if (id) {
      loadProduct();
    }
  }, [id]);

  const loadProduct = async () => {
    try {
      const data = await fetchProduct(id);
      setFormData(data);
    } catch (error) {
      console.error(error);
    }
  };

  const handleChange = (e) => {
    setFormData({ ...formData, [e.target.name]: e.target.value });
  };

  const handleSubmit = async (e) => {
    e.preventDefault();
    try {
      if (id) {
        await updateProduct(id, formData);
      } else {
        await createProduct(formData);
      }
      navigate('/');
    } catch (error) {
      alert('Error saving product');
    }
  };

  return (
    <div className="container" style={{ maxWidth: '600px' }}>
      <button onClick={() => navigate('/')} className="btn" style={{ background: 'transparent', marginBottom: '1rem', padding: 0 }}>
        <ArrowLeft size={20} /> Back to Products
      </button>

      <div className="card">
        <h1>{id ? 'Edit Product' : 'Add New Product'}</h1>
        <form onSubmit={handleSubmit} style={{ marginTop: '2rem' }}>
          <div className="form-group">
            <label>Product Name</label>
            <input
              name="name"
              value={formData.name}
              onChange={handleChange}
              className="form-input"
              required
            />
          </div>
          <div className="form-group">
            <label>Description</label>
            <textarea
              name="description"
              value={formData.description}
              onChange={handleChange}
              className="form-input"
              style={{ minHeight: '100px' }}
            />
          </div>
          <div className="form-group" style={{ display: 'grid', gridTemplateColumns: '1fr 1fr', gap: '1rem' }}>
            <div>
              <label>Price (रु)</label>
              <input
                type="number"
                name="price"
                value={formData.price}
                onChange={handleChange}
                className="form-input"
                required
              />
            </div>
            <div>
              <label>Stock</label>
              <input
                type="number"
                name="stock"
                value={formData.stock}
                onChange={handleChange}
                className="form-input"
                required
              />
            </div>
          </div>
          <div className="form-group">
            <label>Image URL</label>
            <input
              name="image_url"
              value={formData.image_url}
              onChange={handleChange}
              className="form-input"
              placeholder="https://example.com/image.jpg"
            />
          </div>
          <button type="submit" className="btn" style={{ width: '100%', marginTop: '1rem' }}>
            <Save size={20} /> {id ? 'Update Product' : 'Create Product'}
          </button>
        </form>
      </div>
    </div>
  );
}
