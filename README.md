## 启动容器
./bin/example.sh start 03
## 输入ws://127.0.0.1:9502 进行连接
## 在服务端发送测试消息，获取socket_id
## 在客户端输入 [服务端_Socket_id]|[内容] 消息会在服务端显示
http://localhost:8088/wcs/cs.html
http://localhost:8088/ws/wserver.html


后台数据库设计
1 客户端信息表
  ID 自增 ，api-key，域名,uid, enabled,
  start,expired,created,updated.   ##~~sid[socket id]~~

2 客户端消息表【每个客户端对应一个表】
 ID 自增，客户端ID，服务端ID，message，是否已读，类型(发送，接收),发送时间


Block:生成固定socket id.