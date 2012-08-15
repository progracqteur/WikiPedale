 CREATE OR REPLACE FUNCTION getslug(texte varchar) RETURNS varchar AS 
$$
DECLARE
    result varchar;
BEGIN
    result := trim(texte);
    result := replace(texte , 'æ', 'ae');
    result := replace(result , 'œ', 'oe');
    result := replace(result , '€', 'euros');
    result := replace(result , '$', 'dollars');
    result := replace(result , '£', 'pound');
    result := replace(result , '¥', 'yen');
    result := regexp_replace(translate(replace(lower(result), ' ', '-'),
        'áàâãäåāăąÁÂÃÄÅĀĂĄèééêëēĕėęěĒĔĖĘĚìíîïìĩīĭÌÍÎÏÌĨĪĬóôõöōŏőÒÓÔÕÖŌŎŐùúûüũūŭůÙÚÛÜŨŪŬŮçÇÿ&,.ñÑ',
        'aaaaaaaaaaaaaaaaaeeeeeeeeeeeeeeeiiiiiiiiiiiiiiiiooooooooooooooouuuuuuuuuuuuuuuuccy_--nn'), E'[^\\w -]', '', 'g');

    WHILE(position('--' in result) > 0) LOOP
        result = replace(result,'--','-');
    END LOOP;

    RETURN trim(result, '-');
END;
$$
LANGUAGE PLPGSQL;
