CREATE INDEX cities_geog ON zones USING GIST ( polygon ); 
CREATE INDEX place_geog ON place USING GIST ( geom );