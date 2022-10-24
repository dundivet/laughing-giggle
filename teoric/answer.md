# Answer

En primera instancia sería importante determinar los parámetros de ejecución en los que podríamos ejecutar el programa, entre ellos si podría ejecutar múltiples instancias del mismo (o multi-hilo en su defecto), si existen limitaciones en la búsqueda a realizar(dígase profundidad a la que limitar la búsqueda o si solo sería superficial).

Teniendo en cuenta lo antes descrito definiría el entorno de la siguiente forma(teniendo en cuenta que se ejecute de la forma más óptima posible y a la vez simple):

## Environment

- Language: python
- Queue System: RabbitMQ
- Database: SQLite

## Domain vars

- Depth: 10
- Thread: 3
- QuitOnFailure: false
- RabbitMQQueues: ['anwerslist', 'queryurllist']
- timeout: 60s

## Solution

### Program

1. Crear las listas de mensajes en RabbitMQ: `anwerslist`, `queryurllist`.
2. Iniciar la misma cantidad de instancias del `daemon` especificadas en la variable **Thread**.
3. Al iniciar el programa debería poderse leer el archivo **domains.txt**
    3.1 Leer desde los parámetros de ejecución del programa el archivo **domains.txt**
    3.2 Si no es pasado ningún archivo por parámetro, en su defecto leer desde la carpeta donde es ejecutado el programa (ejemplo: /home/usuario/domains.txt)
4. El archivo **domains.txt** debería poderse leer línea por línea, en cada una de ellas debería encontrarse una dirección (url) válida, por lo cual habría que validar lo siguiente(para cada una de las líneas):
    4.1 Validar que la url tenga una estructura válida(nombre, dominio, subdominio, etc).
    4.2 En caso de no tener una estructura válida enviar una notificación al usuario `Estructura de url inválida: ${url}.`
        4.2.1 Si la variable **QuitOnFailure** se encuentra en `true` entonces **terminar la ejecución**.
        4.2.2 Si la variable **QuitOnFailure** se encuentra en `false` entonces saltar la url en cuestión y continuar con la siguiente url.
    4.3 Luego de validar se procede a enviar en forma de evento (hacia la plataforma de mensajería RabbitMQ a través de la lista `queryurllist`) la url a la que se quiere realizar la búsqueda.
5. Luego de "despachadas" todas las url válidas para la búsqueda, se suscribe a la lista de `anwerslist` en la plataforma de mensajería RabbitMQ.
6. LLegados a este punto es necesario tener en cuenta:
    6.1 Los mensajes en forma de evento que comenzarían a llegar(a través de la lista a la que nos encontramos subscritos `anwerslist`).
        6.1.1 Al llegar un nuevo evento, el mismo es procesado obteniendo los datos enviados en el mismo:
            6.1.1.a En caso de llegar un mensaje de error, significaría que la url no pudo ser procesada y por ende no es posible tomar datos de la misma.
            6.1.1.b Son tomadas las cantidad de apariciones de las palabras "perro" y "gato" y sumadas a contadores globales definidos para cada caso.
    6.2 Pasado un tiempo `timeout` (definido como variable de dominio) verificar que la lista de mensajes `queryurllist` se encuentre vacía o no. En el caso de encontrarse vacía significaría que todos los eventos fueron despachados y completados, en caso contrario volveríamos a restablecer el contador de tiempo para verificar nuevamente pasados los segundos indicados por la variable `timeout`.
        6.2.1 En caso de encontrarse todos los mensajes de la lista "completados" nos desuscribimos de la lista `anwerslist` y terminamos el ciclo de espera.
7. Finalmente comparamos las cantidades contadas de las palabras "perro" y "gato" y devolvemos por consola aquella que sea mayor entre los contadores globales.
8. Terminamos las instancias creadas de los `deamons`.
9. Eliminamos las listas de mensajes en RabbitMQ: `anwerslist`, `queryurllist`.
10. Terminamos la ejecución del programa.


### Daemon

1. Al iniciar el demonio lo primero será suscribirse a la lista `queryurllist` de la Plataforma RabbitMQ y comenzar a "escuchar" cuando llegue un evento.
2. Al llegar un nuevo evento, el mismo es procesado obteniendo la url que fue enviada en el mismo.
3. La url es validada haciendo una petición de comprobación que permita verificar si la `Response` es de código 2XX, en caso contrario sucedería lo siguiente:
    3.1 Si es de código 3XX se permitiría un máximo de redirecciones indicadas por la variable `Depth`.
    3.2 Si es de código 4XX o 5XX se devuelve en forma de evento(hacia la plataforma de mensajería RabbitMQ a través de la lista `anwerslist`) que la url solicitada no se encuentra disponible o contiene errores.
4. Una vez completada la validación y teniendo ya el contenido de la página se procede a realizar un crowling al sitio del siguiente modo:
    4.1 Deben contarse todas las apariciones en los textos de las palabras "perro" y "gato", almacenando dicho contador en variables independientes.
    4.2 Obtener todos los enlaces que podrían significar una redirección hacia el propio sitio o enlace de menú que profundice en las páginas del sitio. Para cada uno de estos enlaces debería ejecutarse de forma recursiva los puntos 4.1 y 4.2 teniendo en cuenta la variable `Depth` que define la cantidad de veces que puede "produndizar" **cada ejecución recursiva raíz**.
        4.2.1 Desechar los enlaces que hacen referencia a una dirección externa al sitio.
5. Llegado a este punto deberíamos tener las cantidades de apariciones de las palabras "gato" y "perro" en la url que se encontraba ejecutando el demonio.
6. Por último enviar en forma de evento(hacia la plataforma de mensajería RabbitMQ a través de la lista `anwerslist`) los datos recopilados en las variables "gato" y "perro".
7. Confirmar el mensaje recibido (para que RabbbitMQ pueda darlo por concluido).
8. Volver a "escuchar" los eventos desde la lista `queryurllist`.
