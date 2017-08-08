DELIMITER $$

DROP FUNCTION IF EXISTS `SPLIT_STRING`$$

CREATE FUNCTION `SPLIT_STRING`(delim VARCHAR(12), str VARCHAR(255), pos INT) RETURNS VARCHAR(255) CHARSET utf8 COLLATE utf8_hungarian_ci
    DETERMINISTIC
RETURN
    TRIM(REPLACE(
        SUBSTRING(
            SUBSTRING_INDEX(str, delim, pos),
            LENGTH(SUBSTRING_INDEX(str, delim, pos-1)) + 1
        ),
        delim, ''
    ))$$

DELIMITER ;