﻿
-- Tabla Tarifas para clientes afilia tu empresa */
CREATE TABLE TBL_AFFILIATION_RATE (
	ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'true',
	RESOURCE_NAME VARCHAR(100) NOT NULL COMMENT 'true',
	CLIENT_ID VARCHAR(50) NOT NULL COMMENT 'true',
	QUANTITY_USERS INTEGER NOT NULL COMMENT 'true',
	COST FLOAT(15,2) NOT NULL DEFAULT '0.00' COMMENT 'true',
	IS_BLOCKED BOOLEAN DEFAULT FALSE COMMENT 'true',
	REGISTERED_ON DATETIME NOT NULL COMMENT 'true',
	REGISTERED_BY VARCHAR(50) NOT NULL COMMENT 'true',
	MODIFIED_ON DATETIME ON UPDATE CURRENT_TIMESTAMP COMMENT 'true',
	MODIFIED_BY VARCHAR(100) COMMENT 'true',
	CONSTRAINT PK_TBL_AFFILIATION_RATE PRIMARY KEY (ID)
)
ENGINE = InnoDB
CHARACTER SET utf8 COLLATE utf8_unicode_ci
COMMENT = 'Contents of the rates to affiliate your company';

ALTER TABLE TBL_AFFILIATION_RATE ADD
    CONSTRAINT FK_TBL_AFFILIATION_RATE_RESOURCE_NAME FOREIGN KEY (RESOURCE_NAME)
    REFERENCES TBL_SYSTEM_RESOURCE (RESOURCE_NAME)
    ON DELETE RESTRICT;

ALTER TABLE TBL_AFFILIATION_RATE ADD
    CONSTRAINT FK_TBL_AFFILIATION_RATE_CLIENT_ID FOREIGN KEY (CLIENT_ID)
    REFERENCES TBL_CLIENT (ID)
    ON DELETE RESTRICT;

-- TRIGGER
DELIMITER //

CREATE TRIGGER TRIGGER_AFFILIATION_RATE_B_INSERT BEFORE INSERT ON TBL_AFFILIATION_RATE
FOR EACH ROW
BEGIN
    SET NEW.REGISTERED_ON = NOW();
END;
//

DELIMITER ;


DELIMITER //

CREATE TRIGGER TRIGGER_AFFILIATION_RATE_B_UPDATE BEFORE UPDATE ON TBL_AFFILIATION_RATE
FOR EACH ROW
BEGIN
    SET NEW.MODIFIED_ON = NOW();
END;
//

DELIMITER ;

-------------------------------------------------------*/

-- Valor por usuario para la afiliación 
INSERT INTO TBL_SYSTEM_CONFIGURATION
(KEY_NAME, KEY_VALUE, `KEY_TYPE`, ENCRYPTED, LOAD_INIT, ACCESS_TO, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('USER_AFFILIATE_VALUE', '15000', 0, 0, 0, 90, 0, '2022-12-19 14:24:21', 'carlcaba', NULL, NULL);