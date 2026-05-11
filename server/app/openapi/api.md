# 将军世家 OpenAPI 外部接口文档

> 版本：v1.0 | 更新日期：2026-04-26

---

## 一、概述

将军世家 OpenAPI 是面向外部系统的开放接口，提供用户订单金额、分销上下级关系、佣金数据、粉丝列表、用户信息等核心数据的查询能力。

**基础地址：** `https://www.jiangjunshijia.com/openapi/`

**通信协议：** HTTPS

**请求方式：** GET / POST

**数据格式：** JSON

---

## 二、认证机制

### 2.1 认证方式

采用 **API密钥 + 签名** 认证方式，每次请求必须携带以下 HTTP Header：

| Header名称 | 类型 | 必填 | 说明 |
|------------|------|------|------|
| `app-key` | string | 是 | 应用密钥，由平台分配 |
| `timestamp` | int | 是 | 当前Unix时间戳（秒级），5分钟内有效 |
| `sign` | string | 是 | 请求签名，MD5值（32位小写） |
| `nonce` | string | 否 | 随机字符串，用于防重放攻击（推荐使用） |

### 2.2 签名计算规则

```
签名原文 = app_key + timestamp + 参数按key升序排列拼接 + app_secret
签名结果 = md5(签名原文)
```

**参数拼接规则：**
1. 获取所有请求参数（GET查询参数或POST表单参数）
2. 按参数名（key）进行字典升序排序（ksort）
3. 使用 `http_build_query` 拼接为字符串

### 2.3 签名示例

```
app_key    = jiangjunshijia_open_2026
timestamp  = 1777135997
请求参数    = user_id=1
app_secret = Jjsj@OpenApi#Secret!2026

签名原文 = "jiangjunshijia_open_20261777135997user_id=1Jjsj@OpenApi#Secret!2026"
签名结果 = md5(签名原文)
```

### 2.4 当前密钥信息

| 项目 | 值 |
|------|----|
| app_key | `jiangjunshijia_open_2026` |
| app_secret | `Jjsj@OpenApi#Secret!2026` |
| 签名有效期 | 300秒（5分钟） |

### 2.5 cURL 调用示例

```bash
TIMESTAMP=$(date +%s)
APP_KEY="jiangjunshijia_open_2026"
APP_SECRET="Jjsj@OpenApi#Secret!2026"
PARAMS="user_id=1"
SIGN_STR="${APP_KEY}${TIMESTAMP}${PARAMS}${APP_SECRET}"
SIGN=$(echo -n "$SIGN_STR" | md5sum | awk '{print $1}')

curl -s "https://www.jiangjunshijia.com/openapi/user/orderAmount?user_id=1" \
  -H "app-key: $APP_KEY" \
  -H "timestamp: $TIMESTAMP" \
  -H "sign: $SIGN"
```

### 2.6 PHP 调用示例

```php
<?php
$appKey    = 'jiangjunshijia_open_2026';
$appSecret = 'Jjsj@OpenApi#Secret!2026';
$timestamp = time();

$params = ['user_id' => 1];
ksort($params);
$paramStr = http_build_query($params);

$signStr = $appKey . $timestamp . $paramStr . $appSecret;
$sign    = md5($signStr);

$url = 'https://www.jiangjunshijia.com/openapi/user/orderAmount?' . $paramStr;

$ch = curl_init();
curl_setopt($ch, CURLOPT_URL, $url);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "app-key: {$appKey}",
    "timestamp: {$timestamp}",
    "sign: {$sign}",
]);
$response = curl_exec($ch);
curl_close($ch);

$result = json_decode($response, true);
```

---

## 三、通用响应格式

### 3.1 成功响应

```json
{
    "code": 1,
    "show": 0,
    "msg": "",
    "data": { ... }
}
```

### 3.2 失败响应

```json
{
    "code": 0,
    "show": 0,
    "msg": "错误描述信息",
    "data": []
}
```

### 3.3 字段说明

| 字段 | 类型 | 说明 |
|------|------|------|
| code | int | 状态码，1=成功，0=失败 |
| show | int | 是否前端展示，固定0 |
| msg | string | 错误信息，成功时为空 |
| data | object/array | 业务数据 |

### 3.4 常见错误码

| 错误信息 | 说明 |
|----------|------|
| 缺少必要认证参数(app-key/timestamp/sign) | 请求头缺少认证参数 |
| app-key无效 | app_key值不正确 |
| 请求已过期 | timestamp与服务器时间差超过5分钟 |
| 重复请求 | nonce值重复使用（防重放） |
| 签名验证失败 | sign签名计算不正确 |
| user_id参数必填 | 缺少必填参数 |
| 用户不存在 | 指定用户ID不存在 |

---

## 四、接口详情

---

### 4.1 用户订单金额查询

查询指定用户的订单金额统计及订单列表。

**请求URL：** `GET /openapi/user/orderAmount`

#### 请求参数

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| user_id | int | 是 | 用户ID |
| start_time | string | 否 | 开始时间，格式：2026-01-01 |
| end_time | string | 否 | 结束时间，格式：2026-12-31 |
| order_status | int | 否 | 订单状态（见订单状态枚举） |

#### 订单状态枚举

| 值 | 说明 |
|----|------|
| 0 | 待付款 |
| 1 | 待发货 |
| 2 | 待收货 |
| 3 | 已完成 |
| 4 | 已取消 |
| 5 | 待退款 |

#### 响应数据

```json
{
    "code": 1,
    "show": 0,
    "msg": "",
    "data": {
        "user_info": {
            "user_id": 1,
            "nickname": "安然",
            "mobile": "13800138000",
            "sn": "51213109",
            "total_order_amount": "1280.00",
            "total_order_num": 5,
            "user_money": "100.00",
            "user_earnings": "50.00"
        },
        "statistics": {
            "total_order_amount": 1280.00,
            "total_pay_amount": 1350.00,
            "total_discount_amount": 70.00,
            "total_order_num": 5
        },
        "order_list": [
            {
                "id": 10,
                "sn": "202604260001",
                "order_amount": 256.00,
                "total_amount": 280.00,
                "discount_amount": 24.00,
                "order_status": 3,
                "order_status_desc": "已完成",
                "pay_status": 1,
                "pay_way": 1,
                "pay_time": "2026-04-26 10:30:00",
                "create_time": "2026-04-26 10:28:00"
            }
        ]
    }
}
```

#### 响应字段说明

| 字段路径 | 类型 | 说明 |
|----------|------|------|
| data.user_info.user_id | int | 用户ID |
| data.user_info.nickname | string | 用户昵称 |
| data.user_info.mobile | string | 手机号 |
| data.user_info.sn | string | 用户编号 |
| data.user_info.total_order_amount | string | 用户累计订单金额 |
| data.user_info.total_order_num | int | 用户累计订单数量 |
| data.user_info.user_money | string | 用户余额 |
| data.user_info.user_earnings | string | 用户佣金 |
| data.statistics.total_order_amount | float | 查询范围内订单总金额 |
| data.statistics.total_pay_amount | float | 查询范围内实付总金额 |
| data.statistics.total_discount_amount | float | 查询范围内优惠总金额 |
| data.statistics.total_order_num | int | 查询范围内订单总数 |
| data.order_list[].id | int | 订单ID |
| data.order_list[].sn | string | 订单编号 |
| data.order_list[].order_amount | float | 订单金额 |
| data.order_list[].total_amount | float | 实付金额 |
| data.order_list[].discount_amount | float | 优惠金额 |
| data.order_list[].order_status | int | 订单状态码 |
| data.order_list[].order_status_desc | string | 订单状态描述 |
| data.order_list[].pay_status | int | 支付状态 |
| data.order_list[].pay_way | int | 支付方式 |
| data.order_list[].pay_time | string | 支付时间 |
| data.order_list[].create_time | string | 创建时间 |

---

### 4.2 分销上下级关系查询

查询指定用户的上级链、下级树及分销身份信息。

**请求URL：** `GET /openapi/user/distributionRelation`

#### 请求参数

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| user_id | int | 是 | 用户ID |
| depth | int | 否 | 查询下级深度，1-5，默认3 |

#### 响应数据

```json
{
    "code": 1,
    "show": 0,
    "msg": "",
    "data": {
        "user_info": {
            "user_id": 1,
            "nickname": "安然",
            "mobile": "13800138000",
            "sn": "51213109",
            "code": "U6YTZF",
            "inviter_id": 2
        },
        "superior": {
            "first_leader": {
                "id": 2,
                "sn": "51213110",
                "nickname": "张三",
                "mobile": "13900139000",
                "avatar": "https://xxx/avatar.jpg"
            },
            "second_leader": null,
            "third_leader": null,
            "ancestor_chain": [
                {
                    "id": 2,
                    "sn": "51213110",
                    "nickname": "张三",
                    "mobile": "13900139000"
                }
            ]
        },
        "subordinates": {
            "level": 1,
            "level_name": "一级",
            "count": 3,
            "list": [
                {
                    "id": 68,
                    "sn": "62453849",
                    "nickname": "思拓",
                    "mobile": "",
                    "avatar": "https://xxx/avatar.jpg",
                    "first_leader": 1,
                    "second_leader": 0,
                    "create_time": "2026-04-25 10:33:34",
                    "order_amount": 580.00,
                    "is_distribution": 1,
                    "children": {
                        "level": 2,
                        "level_name": "二级",
                        "count": 2,
                        "list": [
                            {
                                "id": 99,
                                "sn": "62453850",
                                "nickname": "李四",
                                "mobile": "",
                                "avatar": "",
                                "first_leader": 68,
                                "second_leader": 1,
                                "create_time": "2026-04-26 08:00:00",
                                "order_amount": 120.00,
                                "is_distribution": 0,
                                "children": {
                                    "level": 3,
                                    "level_name": "三级",
                                    "count": 0,
                                    "list": []
                                }
                            }
                        ]
                    }
                }
            ]
        },
        "distribution_info": {
            "is_distribution": 1,
            "is_distribution_desc": "分销商",
            "level_id": 1,
            "is_freeze": 0,
            "distribution_time": "2026-04-17 08:35:41"
        }
    }
}
```

#### 响应字段说明

| 字段路径 | 类型 | 说明 |
|----------|------|------|
| data.user_info.code | string | 用户邀请码 |
| data.user_info.inviter_id | int | 邀请人ID |
| data.superior.first_leader | object/null | 一级上级信息，无则为null |
| data.superior.second_leader | object/null | 二级上级信息 |
| data.superior.third_leader | object/null | 三级上级信息 |
| data.superior.ancestor_chain | array | 祖先链（从远到近排列） |
| data.subordinates.level | int | 当前层级（1=一级） |
| data.subordinates.level_name | string | 层级名称（一级/二级/三级） |
| data.subordinates.count | int | 当前层级下级数量 |
| data.subordinates.list[].order_amount | float | 该下级已付订单金额 |
| data.subordinates.list[].is_distribution | int | 是否为分销商（1=是，0=否） |
| data.subordinates.list[].children | object | 递归下级结构，与subordinates格式相同 |
| data.distribution_info.is_distribution | int | 是否为分销商 |
| data.distribution_info.is_distribution_desc | string | 分销身份描述 |
| data.distribution_info.level_id | int | 分销等级ID |
| data.distribution_info.is_freeze | int | 是否冻结 |
| data.distribution_info.distribution_time | string | 成为分销商时间 |

---

### 4.3 分销佣金查询

查询指定用户的佣金统计数据及佣金明细列表。

**请求URL：** `GET /openapi/user/commission`

#### 请求参数

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| user_id | int | 是 | 用户ID |
| start_time | string | 否 | 开始时间，格式：2026-01-01 |
| end_time | string | 否 | 结束时间，格式：2026-12-31 |
| status | int | 否 | 佣金状态：1=待返佣，2=已结算，3=已失效 |

#### 响应数据

```json
{
    "code": 1,
    "show": 0,
    "msg": "",
    "data": {
        "user_info": {
            "user_id": 1,
            "nickname": "安然",
            "user_earnings": "50.00"
        },
        "statistics": {
            "total_earnings": 128.50,
            "settled_earnings": 80.00,
            "unsettled_earnings": 38.50,
            "invalid_earnings": 10.00
        },
        "distribution_level": {
            "name": "大将军王",
            "first_ratio": 20,
            "second_ratio": 5,
            "self_ratio": 0
        },
        "commission_list": [
            {
                "id": 1,
                "user_id": 1,
                "order_goods_id": 10,
                "goods_id": 5,
                "earnings": "25.00",
                "level_id": 1,
                "level": 1,
                "level_desc": "自购佣金",
                "ratio": 0,
                "status": 2,
                "status_desc": "已结算",
                "create_time": "2026-04-20 12:00:00",
                "settlement_time": "2026-04-25 12:00:00",
                "goods_name": "测试商品",
                "goods_price": "199.00",
                "goods_num": 1,
                "total_pay_price": "199.00",
                "buyer_info": {
                    "id": 68,
                    "nickname": "思拓",
                    "mobile": "13800138001"
                }
            }
        ]
    }
}
```

#### 响应字段说明

| 字段路径 | 类型 | 说明 |
|----------|------|------|
| data.statistics.total_earnings | float | 佣金总额 |
| data.statistics.settled_earnings | float | 已结算佣金 |
| data.statistics.unsettled_earnings | float | 未结算佣金 |
| data.statistics.invalid_earnings | float | 已失效佣金 |
| data.distribution_level.name | string | 分销等级名称 |
| data.distribution_level.first_ratio | int | 一级分佣比例(%) |
| data.distribution_level.second_ratio | int | 二级分佣比例(%) |
| data.distribution_level.self_ratio | int | 自购佣金比例(%) |
| data.commission_list[].level | int | 佣金层级：0=自购，1=一级，2=二级 |
| data.commission_list[].level_desc | string | 层级描述 |
| data.commission_list[].status | int | 状态：1=待返佣，2=已结算，3=已失效 |
| data.commission_list[].status_desc | string | 状态描述 |
| data.commission_list[].goods_name | string | 商品名称 |
| data.commission_list[].goods_price | float | 商品单价 |
| data.commission_list[].goods_num | int | 商品数量 |
| data.commission_list[].total_pay_price | float | 实付金额 |
| data.commission_list[].buyer_info | object/null | 下单买家信息 |

---

### 4.4 粉丝列表查询

查询指定用户的粉丝（下级会员）列表，支持分页和筛选。

**请求URL：** `GET /openapi/user/fans`

#### 请求参数

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| user_id | int | 是 | 用户ID |
| type | string | 否 | 粉丝类型：`all`=全部，`first`=仅一级，`second`=仅二级，默认`all` |
| keyword | string | 否 | 搜索关键词（匹配昵称/手机号/编号） |
| page_no | int | 否 | 页码，默认1 |
| page_size | int | 否 | 每页数量，默认25 |

#### 响应数据

```json
{
    "code": 1,
    "show": 0,
    "msg": "",
    "data": {
        "user_info": {
            "user_id": 1,
            "nickname": "安然"
        },
        "statistics": {
            "total_fans": 15,
            "first_level_fans": 10,
            "second_level_fans": 5
        },
        "list": [
            {
                "id": 68,
                "sn": "62453849",
                "nickname": "思拓",
                "mobile": "13800138001",
                "avatar": "https://xxx/avatar.jpg",
                "first_leader": 1,
                "second_leader": 0,
                "create_time": "2026-04-25 10:33:34",
                "order_amount": 580.00,
                "order_num": 3,
                "is_distribution": true,
                "relation": "一级粉丝"
            }
        ],
        "page": {
            "page_no": 1,
            "page_size": 25,
            "total": 15,
            "pages": 1
        }
    }
}
```

#### 响应字段说明

| 字段路径 | 类型 | 说明 |
|----------|------|------|
| data.statistics.total_fans | int | 粉丝总数 |
| data.statistics.first_level_fans | int | 一级粉丝数 |
| data.statistics.second_level_fans | int | 二级粉丝数 |
| data.list[].order_amount | float | 该粉丝已付订单金额 |
| data.list[].order_num | int | 该粉丝已付订单数量 |
| data.list[].is_distribution | bool | 是否为分销商 |
| data.list[].relation | string | 关系描述：一级粉丝/二级粉丝 |
| data.page.page_no | int | 当前页码 |
| data.page.page_size | int | 每页数量 |
| data.page.total | int | 总记录数 |
| data.page.pages | int | 总页数 |

---

### 4.5 用户信息查询

查询指定用户的完整信息，支持多种查询方式。

**请求URL：** `GET /openapi/user/info`

#### 请求参数

| 参数名 | 类型 | 必填 | 说明 |
|--------|------|------|------|
| user_id | int | 否 | 用户ID（三选一） |
| mobile | string | 否 | 手机号（三选一） |
| sn | string | 否 | 用户编号（三选一） |

> **注意：** `user_id`、`mobile`、`sn` 三个参数必须提供其中一个，优先级为 user_id > mobile > sn。

#### 响应数据

```json
{
    "code": 1,
    "show": 0,
    "msg": "",
    "data": {
        "id": 1,
        "sn": "51213109",
        "nickname": "安然",
        "avatar": "https://www.jiangjunshijia.com/uploads/images/avatar.jpg",
        "mobile": "13800138000",
        "sex": "未知",
        "user_money": "100.00",
        "user_integral": "8.00",
        "user_earnings": "50.00",
        "total_order_amount": "1280.00",
        "total_order_num": 5,
        "first_leader": 2,
        "second_leader": 0,
        "third_leader": 0,
        "code": "U6YTZF",
        "inviter_id": 2,
        "register_source": 1,
        "create_time": "2026-03-31 10:11:45",
        "distribution": {
            "is_distribution": 1,
            "level_id": 1,
            "is_freeze": 0,
            "distribution_time": "2026-04-17 08:35:41"
        },
        "first_leader_info": {
            "id": 2,
            "sn": "51213110",
            "nickname": "张三",
            "mobile": "13900139000"
        }
    }
}
```

#### 响应字段说明

| 字段路径 | 类型 | 说明 |
|----------|------|------|
| data.id | int | 用户ID |
| data.sn | string | 用户编号 |
| data.nickname | string | 昵称 |
| data.avatar | string | 头像URL |
| data.mobile | string | 手机号 |
| data.sex | string | 性别 |
| data.user_money | string | 用户余额 |
| data.user_integral | string | 用户积分 |
| data.user_earnings | string | 用户佣金 |
| data.total_order_amount | string | 累计订单金额 |
| data.total_order_num | int | 累计订单数量 |
| data.first_leader | int | 一级上级ID |
| data.second_leader | int | 二级上级ID |
| data.third_leader | int | 三级上级ID |
| data.code | string | 用户邀请码 |
| data.inviter_id | int | 邀请人ID |
| data.register_source | int | 注册来源 |
| data.create_time | string | 注册时间 |
| data.distribution | object/null | 分销信息，非分销商时为null |
| data.distribution.is_distribution | int | 是否为分销商 |
| data.distribution.level_id | int | 分销等级ID |
| data.distribution.is_freeze | int | 是否冻结 |
| data.distribution.distribution_time | string | 成为分销商时间 |
| data.first_leader_info | object/null | 一级上级用户信息 |

---

## 五、接口总览

| 序号 | 接口名称 | 请求URL | 方法 | 核心功能 |
|------|----------|---------|------|----------|
| 1 | 用户订单金额查询 | /openapi/user/orderAmount | GET | 查询用户订单金额统计及明细 |
| 2 | 分销上下级关系查询 | /openapi/user/distributionRelation | GET | 查询用户上级链和下级树 |
| 3 | 分销佣金查询 | /openapi/user/commission | GET | 查询用户佣金统计及明细 |
| 4 | 粉丝列表查询 | /openapi/user/fans | GET | 查询用户粉丝列表（分页） |
| 5 | 用户信息查询 | /openapi/user/info | GET | 查询用户完整信息 |

---

## 六、文件结构

```
server/app/openapi/
├── controller/
│   ├── BaseOpenController.php          # 基础控制器
│   └── UserController.php              # 用户接口控制器（5个接口）
├── http/
│   └── middleware/
│       ├── InitMiddleware.php          # 初始化中间件（控制器解析）
│       └── ApiAuthMiddleware.php       # API签名认证中间件
├── logic/
│   └── OpenApiLogic.php                # 业务逻辑层（核心数据处理）
└── config/
    └── route.php                       # 中间件配置

public/
└── openapi.php                          # OpenAPI入口文件
```

---

## 七、安全说明

1. **签名验证**：所有请求必须通过MD5签名验证，防止参数篡改
2. **时间戳校验**：请求时间戳与服务器时间差超过5分钟即拒绝，防止重放攻击
3. **Nonce防重放**：建议每次请求携带唯一nonce值，服务器会缓存5分钟内已使用的nonce
4. **HTTPS传输**：所有接口强制使用HTTPS协议，确保传输安全
5. **密钥保密**：app_secret仅用于服务端签名计算，不可暴露在前端代码中
