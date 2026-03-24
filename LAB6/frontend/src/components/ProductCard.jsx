import React from 'react';
import { Link } from 'react-router';
import { Trash2, Edit } from 'lucide-react';

export default function ProductCard({ product, onDelete }) {
  return (
    <div className="card">
      {product.image_url && (
        <img 
          src={product.image_url} 
          alt={product.name} 
          style={{ width: '100%', height: '200px', objectFit: 'cover', borderRadius: '1rem', marginBottom: '1rem' }}
        />
      )}
      <h2 style={{ marginBottom: '0.5rem' }}>{product.name}</h2>
      <p style={{ color: 'var(--text-muted)', fontSize: '0.9rem', marginBottom: '1rem', height: '3rem', overflow: 'hidden' }}>
        {product.description}
      </p>
      <div style={{ display: 'flex', justifyContent: 'space-between', alignItems: 'center' }}>
        <span style={{ fontSize: '1.5rem', fontWeight: '700', color: 'var(--secondary)' }}>
          रु{product.price}
        </span>
        <div style={{ display: 'flex', gap: '0.5rem' }}>
          <Link to={`/edit/${product.id}`} className="btn" style={{ padding: '0.5rem', background: 'var(--bg-card)' }}>
            <Edit size={18} />
          </Link>
          <button 
            onClick={() => onDelete(product.id)} 
            className="btn btn-danger" 
            style={{ padding: '0.5rem' }}
          >
            <Trash2 size={18} />
          </button>
        </div>
      </div>
    </div>
  );
}
