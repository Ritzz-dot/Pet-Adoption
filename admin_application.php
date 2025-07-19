SELECT a.*, u.fullname AS applicant_name, p.name AS pet_name
FROM adoption_application_detail a
JOIN users u ON a.user_id = u.id
JOIN pets p ON a.pet_id = p.pet_id
ORDER BY a.app_id DESC;
