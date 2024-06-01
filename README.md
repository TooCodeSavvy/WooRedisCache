# WooRedisCache


Het idee:

+---------------------+
|    IRedisClient     |
+---------------------+
| + set()             |
| + get()             |
| + delete()          |
+---------------------+

+---------------------+
|    RedisClient      |
+---------------------+
| - redisConnection   |
+---------------------+
| + __construct()      |
| + set()             |
| + get()             |
| + delete()          |
+---------------------+

+-------------------+
|   CustomPlugin    |
+-------------------+
| - redisClient: IRedisClient |
+-------------------+
| + __construct(redisClient: IRedisClient) |
| + indexProduct()   |
| + getProduct()     |
| + addToCart()      |
| + removeFromCart() |
+-------------------+
