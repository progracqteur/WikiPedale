insert into cities (id, name, codeprovince, polygon, slug, center)  
   select gid, nom, nurgcdl2, geog, '', ST_Centroid(ST_geomFromText(ST_AsText(geog))) from limites where nom is not null;
update cities set slug = getslug(name);