Tabla: Suscripciones

ID_Suscripcion (Clave primaria): Identificador único de la suscripción.
ID_Cliente: Identificador del cliente asociado a la suscripción.
Fecha_Inicio: Fecha en que la suscripción comenzó.
Plan: Plan de suscripción (puede ser básico, premium, etc.).
Estado: Estado de la suscripción (activo, cancelado, en espera, etc.).
Monto: Monto de la suscripción mensual.
Metodo_Pago: Método de pago utilizado para la suscripción (tarjeta de crédito, PayPal, etc.).


Tabla: Pagos

ID_Pago (Clave primaria): Identificador único del pago.
ID_Suscripcion (Clave foránea): Identificador de la suscripción asociada al pago.
Fecha_Pago: Fecha en que se realizó el pago.
Monto_Pagado: Monto pagado en esta transacción.
Estado: Estado del pago (aprobado, pendiente, rechazado, etc.).

Tabla: Tarjetas_Credito

ID_Tarjeta (Clave primaria): Identificador único de la tarjeta de crédito.
ID_Cliente (Clave foránea): Identificador del cliente asociado a la tarjeta.
Numero_Tarjeta: Últos cuatro dígitos o un token de la tarjeta de crédito.
Fecha_Expiracion: Fecha de expiración de la tarjeta.
Tipo_Tarjeta: Tipo de tarjeta (Visa, MasterCard, etc.).
Marca: Marca de la tarjeta (American Express, Discover, etc.).
Metodo_Pago_Predefinido: Indicador para saber si esta tarjeta es la predeterminada para cobros automáticos.