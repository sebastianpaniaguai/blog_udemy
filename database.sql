CREATE DATABASE IF NOT EXISTS api_rest_laravel;
USE api_rest_laravel;

CREATE TABLE users(
  id  int(255) auto_increment not null,
  name  VARCHAR(50) not null,
  surname  VARCHAR(100),
  role  VARCHAR(20),
  email  VARCHAR(255) not null,
  password   VARCHAR(255) not null,
  description text,
  image   VARCHAR(255),
  created_at  datetime DEFAULT NULL,
  updated_at datetime DEFAULT NULL,
  remember_token VARCHAR(255),
  CONSTRAINT pk_users PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE categories(
  id  int(255) auto_increment not null,
  name  VARCHAR(100) not null,
  created_at  datetime DEFAULT NULL,
  updated_at datetime DEFAULT NULL,
  CONSTRAINT pk_categories PRIMARY KEY(id)
)ENGINE=InnoDb;

CREATE TABLE posts(
  id  int(255) auto_increment not null,
  user_id int(255) not null,
  category_id int(255) not null,
  title VARCHAR(255),
  content text not null,
  image VARCHAR(255),
  created_at  datetime DEFAULT NULL,
  updated_at datetime DEFAULT NULL,
  CONSTRAINT pk_posts PRIMARY KEY(id),
  CONSTRAINT fk_posts_users FOREIGN KEY(user_id) REFERENCES users(id),
  CONSTRAINT fk_posts_categories FOREIGN KEY(category_id) REFERENCES categories(id)
)ENGINE=InnoDb;
