insert into cities (id, name, codeprovince, polygon, slug)  
   select gid, nom, nurgcdl2, geog, '' from limites where nom is not null;
update cities set slug = getslug(name);