UPDATE place p
SET term = c.term
FROM place_category pc
JOIN categories c ON
c.id = pc.category_id
WHERE p.id = pc.place_id
