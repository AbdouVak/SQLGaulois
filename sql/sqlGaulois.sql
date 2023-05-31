-- 1. Nom des lieux qui finissent par 'um'.
SELECT nom_lieu FROM lieu

WHERE nom_lieu LIKE '%um';

-- 2. Nombre de personnages par lieu (trié par nombre de personnages décroissant).-- -- -- CORRIGE

SELECT lieu.nom_lieu,COUNT(id_personnage) AS nbHabitants

FROM personnage,lieu

WHERE personnage.id_lieu = lieu.id_lieu

GROUP BY lieu.id_lieu
ORDER BY nbHabitants DESC;

-- 3. Nom des personnages + spécialité + adresse et lieu d'habitation, triés par lieu puis par nom de personnage.
-- CORRIGE
SELECT nom_personnage, nom_specialite , adresse_personnage 

FROM personnage,specialite

WHERE personnage.id_specialite = specialite.id_specialite

ORDER BY id_lieu,nom_personnage;

-- 4. Nom des spécialités avec nombre de personnages par spécialité (trié par nombre de
-- personnages décroissant).
SELECT nom_specialite,COUNT(personnage.id_specialite) AS persoSpecialite

FROM specialite
RIGHT JOIN personnage
ON specialite.id_specialite = personnage.id_specialite

GROUP BY personnage.id_specialite
ORDER BY persoSpecialite DESC;

-- 5. Nom, date et lieu des batailles, classées de la plus récente à la plus ancienne (dates affichées
-- au format jj/mm/aaaa).
SELECT nom_bataille,DATE_FORMAT(date_bataille, "%d/%m/%Y"),nom_lieu

FROM bataille
RIGHT JOIN lieu
ON bataille.id_lieu = lieu.id_lieu

ORDER  BY date_bataille DESC;

-- 6. Nom des potions + coût de réalisation de la potion (trié par coût décroissant).
-- CORRIGE
SELECT nom_potion,
	SUM(cout_ingredient*qte) AS coutTotal
	
FROM potion
LEFT JOIN composer
ON potion.id_potion = composer.id_potion
LEFT JOIN ingredient
ON ingredient.id_ingredient = composer.id_ingredient

GROUP BY potion.id_potion
ORDER BY coutTotal DESC;

-- 7. Nom des ingrédients + coût + quantité de chaque ingrédient qui composent la potion 'Santé'.

SELECT nom_ingredient,cout_ingredient*qte,qte,nom_potion

FROM potion

JOIN composer
ON potion.id_potion = composer.id_potion

JOIN ingredient
ON ingredient.id_ingredient = composer.id_ingredient

WHERE nom_potion = 'Santé';

-- 8. Nom du ou des personnages qui ont pris le plus de casques dans la bataille 'Bataille du village
-- gaulois'.
SELECT p.nom_personnage, SUM(pc.qte) AS nb_casques 

FROM personnage p, bataille b, prendre_casque pc

WHERE p.id_personnage = pc.id_personnage AND pc.id_bataille = b.id_bataille AND b.nom_bataille = 'Bataille du village gaulois' 
GROUP BY p.id_personnage 

HAVING nb_casques >= ALL( SELECT SUM(pc.qte) 

FROM prendre_casque pc, bataille b 

WHERE b.id_bataille = pc.id_bataille AND b.nom_bataille = 'Bataille du village gaulois' 
GROUP BY pc.id_personnage );

-- 9. Nom des personnages et leur quantité de potion bue (en les classant du plus grand buveur
-- au plus petit).
SELECT nom_personnage,SUM(dose_boire) AS totalDose
FROM boire ,personnage
WHERE personnage.id_personnage = boire.id_personnage
GROUP BY personnage.id_personnage
ORDER BY totalDose DESC;

-- 10. Nom de la bataille où le nombre de casques pris a été le plus important.
SELECT b.nom_bataille

FROM bataille b, prendre_casque pc

WHERE pc.id_bataille = b.id_bataille
GROUP BY b.id_bataille

HAVING SUM(pc.qte)  >= ALL( SELECT SUM(pc.qte) 

FROM prendre_casque pc, bataille b 

WHERE b.id_bataille = pc.id_bataille
GROUP BY b.id_bataille );

-- 11. Combien existe-t-il de casques de chaque type et quel est leur coût tota (classés par nombre décroissant)
-- CORRIGE
SELECT SUM(prendre_casque.qte),nom_type_casque, SUM(cout_casque)

FROM type_casque , casque, prendre_casque
WHERE casque.id_type_casque = type_casque.id_type_casque 
AND casque.id_casque = prendre_casque.id_casque

GROUP BY nom_type_casque
ORDER BY SUM(cout_casque) DESC;

-- 12. Nom des potions dont un des ingrédients est le poisson frais.
SELECT nom_potion

FROM potion p,composer c,ingredient i
WHERE p.id_potion = c.id_potion AND c.id_ingredient = i.id_ingredient AND i.nom_ingredient = 'Poisson frais';


-- 13. Nom du / des lieu(x) possédant le plus d'habitants, en dehors du village gaulois.
SELECT l.nom_lieu,COUNT(p.id_lieu) AS nbHabitants

FROM lieu l, personnage p
WHERE l.id_lieu = p.id_lieu

GROUP BY p.id_lieu

HAVING nbHabitants >= ALL( SELECT COUNT(p.id_lieu) 

FROM lieu l, personnage p
WHERE l.id_lieu = p.id_lieu

GROUP BY p.id_lieu );

-- 14. Nom des personnages qui n'ont jamais bu aucune potion.
--CORRGIE
SELECT personnage.nom_personnage 

FROM personnage
WHERE personnage.id_personnage NOT IN (SELECT id_personnage
	FROM boire);


-- 15. Nom du / des personnages qui n'ont pas le droit de boire de la potion 'Magique'.
SELECT p.nom_personnage

FROM personnage p, autoriser_boire ab
WHERE p.id_personnage NOT IN (SELECT id_personnage 
	FROM autoriser_boire
	WHERE id_potion = 1)

GROUP BY p.id_personnage;

-- A. Ajoutez le personnage
INSERT INTO personnage VALUES (46,
	 'Champbelix',
	 'ferme Hantassion ',
	 'indisponible.png',
	 6,
	 12);

-- B. Autorisez Bonemine à boire de la potion magique, elle est jalouse d'Iélosubmarine...
INSERT INTO autoriser_boire (id_potion,id_personnage) 
VALUES (1,12) 

-- C. Supprimez les casques grecs qui n'ont jamais été pris lors d'une bataille.
-- CORRIGE
SELECT casque.id_casque,nom_casque,casque.id_type_casque

FROM casque,type_casque
WHERE casque.id_type_casque = type_casque.id_type_casque

AND casque.id_casque NOT IN (SELECT id_casque 
	FROM prendre_casque)
	
AND casque.id_type_casque = 2;

-- D. Modifiez l'adresse de Zérozérosix : il a été mis en prison à Condate
UPDATE personnage
SET adresse_personnage = 'prison à Condate'
WHERE nom_personnage = 'Zérozérosix';

-- E. La potion 'Soupe' ne doit plus contenir de persil
DELETE FROM composer
WHERE id_potion = 9 AND id_ingredient = 19;

-- F. Obélix s'est trompé : ce sont 42 casques Weisenau, et non Ostrogoths
-- CORRIGE
UPDATE prendre_casque
SET id_casque=10, qte=42
WHERE id_personnage = 5 AND id_bataille=9;
