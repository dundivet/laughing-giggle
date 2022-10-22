/* EJERCICIO 1 */

SELECT 
  p.name AS name, 
  COUNT(b.id) AS amount 
FROM people AS p 
LEFT JOIN books AS b ON p.id = b.owner_id 
GROUP BY p.name 
ORDER BY amount DESC, name ASC;

/* EJERCICIO 2 */

SELECT
  b.title AS title,
  owner.age AS owner_age,
  author.age AS author_age
FROM books AS b
INNER JOIN people AS owner ON b.owner_id = owner.id
INNER JOIN people AS author ON b.author_id = author.id

/* EJERCICIO 3 */

SELECT 
  p.name AS name 
FROM people AS p
FROM (SELECT COUNT(b1) AS ctty FROM books AS b1 WHERE b1.owner_id = p.id) AS owning
FROM (SELECT COUNT(b2) AS ctty FROM books AS b2 WHERE b2.author_id = p.id) AS authoring
WHERE authoring.ctty > owning.ctty;

/* EJERCICIO 4 */

UPDATE people AS p
  (SELECT ) AS b
SET p.favorite_author_id = b.author_id
WHERE p.id = b.owner_id;