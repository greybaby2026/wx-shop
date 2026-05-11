/**
 * 枚举
 */


/* 消息类型 */
export const E_Msg = {
    TEXT: 1,
    IMAGE: 2,
	GOODS: 3,
}

/* 消息Event */
export const E_MsgEvent = {
    CHAT: 'chat',
    PING: 'ping',
    ERROR: 'error',
    NOTICE: 'notice',
    USER_ONLINE: 'user_online',
    TRANSFER: 'transfer',

}

/* 加载状态 */
export const E_Load = {
    LOAD: 1,
    NORMAL: 2,
    ERROR: 3,
    EMPTY: 0,
}

export const terminal = 4