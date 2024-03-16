﻿
-- Tabla Tarifas para clientes afilia tu empresa */
CREATE TABLE TBL_AFFILIATION_RATE (
	ID INTEGER UNSIGNED NOT NULL AUTO_INCREMENT COMMENT 'true',
	RESOURCE_NAME VARCHAR(100) NOT NULL COMMENT 'true',
	CLIENT_ID VARCHAR(50) NOT NULL COMMENT 'true',
	SUBSCRIPTION_ID INTEGER UNSIGNED NOT NULL COMMENT 'false',
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

ALTER TABLE TBL_AFFILIATION_RATE ADD
    CONSTRAINT FK_TBL_AFFILIATION_RATE_SUBSCRIPTION_ID FOREIGN KEY (SUBSCRIPTION_ID)
    REFERENCES TBL_AFFILIATE_SUBSCRIPTION (ID)
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

-- ----------------------------------------------------*/

-- Valor por usuario para la afiliación 
INSERT INTO TBL_SYSTEM_CONFIGURATION
(KEY_NAME, KEY_VALUE, `KEY_TYPE`, ENCRYPTED, LOAD_INIT, ACCESS_TO, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('USER_AFFILIATE_BASIC_RATE', '375000', 0, 0, 0, 90, 0, '2022-12-19 14:24:21', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_CONFIGURATION
(KEY_NAME, KEY_VALUE, `KEY_TYPE`, ENCRYPTED, LOAD_INIT, ACCESS_TO, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('USER_AFFILIATE_ALLIED_COMPANY', '275000', 0, 0, 0, 90, 0, '2022-12-19 14:24:21', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_CONFIGURATION
(KEY_NAME, KEY_VALUE, `KEY_TYPE`, ENCRYPTED, LOAD_INIT, ACCESS_TO, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('USER_AFFILIATE_COMPANY_USERS', '15000', 0, 0, 0, 90, 0, '2022-12-19 14:24:21', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_CONFIGURATION
(KEY_NAME, KEY_VALUE, `KEY_TYPE`, ENCRYPTED, LOAD_INIT, ACCESS_TO, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('USER_AFFILIATE_DELIVERY_ALLIED', '15000', 0, 0, 0, 90, 0, '2022-12-19 14:24:21', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_CONFIGURATION
(KEY_NAME, KEY_VALUE, `KEY_TYPE`, ENCRYPTED, LOAD_INIT, ACCESS_TO, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('MAX_USERS_AFFILIATION_BASIC_RATE', '1', 0, 0, 0, 90, 0, '2022-12-19 14:24:21', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_CONFIGURATION
(KEY_NAME, KEY_VALUE, `KEY_TYPE`, ENCRYPTED, LOAD_INIT, ACCESS_TO, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('MAX_USERS_AFFILIATION_ALLIED_COMPANY', '9', 0, 0, 0, 90, 0, '2022-12-19 14:24:21', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_CONFIGURATION
(KEY_NAME, KEY_VALUE, `KEY_TYPE`, ENCRYPTED, LOAD_INIT, ACCESS_TO, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('MAX_USERS_AFFILIATION_COMPANY', '60', 0, 0, 0, 90, 0, '2022-12-19 14:24:21', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_CONFIGURATION
(KEY_NAME, KEY_VALUE, `KEY_TYPE`, ENCRYPTED, LOAD_INIT, ACCESS_TO, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('MAX_USERS_AFFILIATION_DELIVERY_ALLIED', '250', 0, 0, 0, 90, 0, '2022-12-19 14:24:21', 'carlcaba', NULL, NULL);


-- ----------------------------------*/

-- Texto de afiliación de cliente aliado paso 1
INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP_LABEL_1', 'Bienvenida', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP_LABEL_2', 'Personaliza tu Plan', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP_LABEL_3', 'Confirmar compra', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_TITLE_MODAL', 'Pasos para la afiliación', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP1_H2', 'Desde aquí podrás gestionar tu afiliación', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP1_P', 'Recuerda que deberás adquirir un servicio de afiliación para tu empresa, cada una de las empresas aliadas que trabajen contigo y adquirir membresías para usuarios con una base mensual.', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_ACCEPT_TERMS_CONDITIONS', '* Acepta los términos y condiciones de tu plan Vincula tu Aliado.', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_NEXT_BUTTON', 'Siguiente', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_PREVIOUS_BUTTON', 'Anterior', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_BTN_START_HERE', 'Comienza aquí', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

-- -------------------------------------------*/

-- Texto de afiliación de cliente aliado paso 2

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_NAME_BASIC', 'Tarifa básica usuario cliente *:', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_NAME_BASIC_2', 'Agrega una empresa Cliente', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_NAME_1', 'Agrega una empresa aliada de mensajería adicional:', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_NAME_2', 'Agrega usuarios para tu compañía*', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_NAME_3', 'Agrega mensajeros a tu empresa mensajería Aliada', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP2_H2', 'Conocer el valor y elegir la cantidad de aliados y usuarios', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP2_H4', 'Adiciona empresas y usuarios de tu plataforma aquí', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP2_TB_COL1', 'Productos', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP2_TB_COL2', 'Precio', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP2_TB_COL3', 'Cantidad', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP2_TB_COL4', 'Total', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP2_LB_TOTAL_VALUE', 'Total de tu suscripción mensual', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

-- Texto de afiliación de cliente aliado paso 3

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP3_TITLE_BILLING_DATA', 'Tus Datos de Facturación', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP3_INPUT_BUSINESS_NAME', 'Razón Social', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP3_INPUT_NIT', 'NIT', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP3_INPUT_MAIN_PHONE', 'Teléfono Principal', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP3_INPUT_MAIN_ADDRESS', 'Dirección Principal', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP3_TITLE_CARD_DETAILS', 'Detalles de la tarjeta', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('FORMS_VALIDATION_REQUIRED', 'El campo :attribute es obligatorio.', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);


-- Texto de afiliación de cliente aliado paso 4

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP4_WELCOME_TO_UBIO', '¡Bienvenido(a) a UBIO!', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP4_DESCRIPTION', '<h5>¡Tu suscripción ha sido exitosa!</h5><p>Ahora podrás configurar tus usuarios, empresa aliada y comenzar a gestionar tus envíos de mensajería de manera inteligente.</p><p>Comienza por registrar tu empresa aliada y tus usuarios. Recuerda que también podrás realizar estos pasos desde el menú lateral en la opción GESTIÓN XXX</p>', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP4_WHAT_TO_DO_FIRST', '¿Qué quieres hacer primero?', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP4_REGISTER_ALLIED_COMPANY', 'Registrar mi empresa aliada.', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP4_ADD_MY_USERS', 'Configurar mis usuarios.', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP4_OTHER_OPTIONS', 'Otras opciones', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP4_ACQUIRE_MEMBERSHIPS', 'Adquirir membresías para otros nit*.', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP4_VIEW_MY_SUBSCRIPTION', 'Ver mi suscripción.', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);

INSERT INTO TBL_SYSTEM_RESOURCE
(RESOURCE_NAME, RESOURCE_TEXT, LANGUAGE_ID, IS_SYSTEM, IS_BLOCKED, REGISTERED_ON, REGISTERED_BY, MODIFIED_ON, MODIFIED_BY)
VALUES('AFFILIATION_RATE_STEP4_CANCEL_MY_SUBSCRIPTION', 'Cancelar mi suscripción.', 2, 1, 0, '2022-12-19 14:24:23', 'carlcaba', NULL, NULL);