CREATE DATABASE "makkelijkemarkt";
CREATE USER "makkelijkemarkt" WITH PASSWORD 'makkelijkemarkt';
GRANT ALL PRIVILEGES ON DATABASE "makkelijkemarkt" to "makkelijkemarkt";
CREATE USER "makkelijkemarkt_read" WITH PASSWORD 'makkelijkemarkt_read';
GRANT ALL PRIVILEGES ON DATABASE "makkelijkemarkt" to "makkelijkemarkt_read";
\c "makkelijkemarkt"
CREATE EXTENSION "uuid-ossp";