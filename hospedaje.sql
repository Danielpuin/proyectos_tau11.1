
CREATE TABLE usuarios (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario VARCHAR(50) NOT NULL UNIQUE,
  correo VARCHAR(100) NOT NULL UNIQUE,
  contrasena VARCHAR(255) NOT NULL,
  fecha_registro TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);


CREATE TABLE hoteles (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  telefono VARCHAR(30),
  direccion VARCHAR(150),
  precio_min DECIMAL(10,2),
  precio_max DECIMAL(10,2),
  descripcion_precio VARCHAR(255) 
);


CREATE TABLE cabanas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nombre VARCHAR(100) NOT NULL,
  telefono VARCHAR(30),
  direccion VARCHAR(150),
  precio DECIMAL(10,2),
  descripcion_precio VARCHAR(255) 


CREATE TABLE calificaciones (
  id INT AUTO_INCREMENT PRIMARY KEY,
  usuario_id INT NOT NULL,
  establecimiento_id INT NOT NULL,
  tipo_establecimiento ENUM('hotel', 'cabana') NOT NULL,
  puntuacion INT NOT NULL,
  fecha_creacion TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE,
  UNIQUE KEY `rating_unique` (`usuario_id`, `establecimiento_id`, `tipo_establecimiento`)
);