import React from 'react';
import { Routes, Route } from 'react-router';
import HomePage from './pages/HomePage';
import ProductFormPage from './pages/ProductFormPage';

export default function App() {
  return (
    <div>
      <nav>
        <div className="nav-content">
          <div className="logo">SMARTSHOP</div>
        </div>
      </nav>
      
      <Routes>
        <Route path="/" element={<HomePage />} />
        <Route path="/add" element={<ProductFormPage />} />
        <Route path="/edit/:id" element={<ProductFormPage />} />
      </Routes>
    </div>
  );
}
