﻿/* SELECT THE SCHEMA */
USE VTAPPCORP;

/* CLIENT TABLE */
ALTER TABLE TBL_CLIENT ADD INDEX INDX_CLIENT_NAME(CLIENT_NAME);
ALTER TABLE TBL_CLIENT ADD INDEX INDX_CLIENT_EMAIL(EMAIL);

/* DIRECT_CHAT TABLE */
ALTER TABLE TBL_DIRECT_CHAT ADD INDEX INDX_DIRECT_CHAT_SENDER(SENDER);
ALTER TABLE TBL_DIRECT_CHAT ADD INDEX INDX_DIRECT_CHAT_DESTINIY(DESTINY);

/* EMPLOYEE TABLE */
ALTER TABLE TBL_EMPLOYEE ADD INDEX INDX_EMPLOYEE_LASTNAME(LAST_NAME);
ALTER TABLE TBL_EMPLOYEE ADD INDEX INDX_EMPLOYEE_IDENTIFICATION(IDENTIFICATION);
ALTER TABLE TBL_EMPLOYEE ADD INDEX INDX_EMPLOYEE_LOCATION(LATITUDE,LONGITUDE);
ALTER TABLE TBL_EMPLOYEE ADD INDEX INDX_EMPLOYEE_EMAIL(EMAIL);

/* PARTNER NAME */
ALTER TABLE TBL_PARTNER ADD INDEX INDX_PARTNER_NAME(PARTNER_NAME);
ALTER TABLE TBL_PARTNER ADD INDEX INDX_PARTNER_IDENTIFICATION(IDENTIFICATION);

/* ZONE */
ALTER TABLE TBL_SYSTEM_ZONE ADD INDEX INDX_ZONE_NAME(ZONE_NAME);

/* SYSTEM_USER */
ALTER TABLE TBL_SYSTEM_USER ADD INDEX INDX_REFERENCE(REFERENCE);

/* SERVICE */
ALTER TABLE TBL_SERVICE ADD INDEX INDX_REGISTERED_ON(REGISTERED_ON);

/* SERVICE LOG */
ALTER TABLE TBL_SERVICE_LOG ADD INDEX INDX_SL_REGISTERED_ON(REGISTERED_ON);
