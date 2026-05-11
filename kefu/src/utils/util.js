
/**
 * @description 节流
 * @param fn { Function } 方法
 * @param time { Number } 间隔时间
 * @return { Function } 节流处理的后的方法
 */
export const throttle = (
    fn,
    time = 1000
) => {
    let previousTime = new Date(0).getTime();
    return (...args ) => {
        let nowTime = new Date().getTime();
        if (nowTime - previousTime > time) {
            previousTime = nowTime;
            return fn(args);
        }
    }
}


/**
 * @description 防抖
 * @param fn { Function } 方法
 * @param time { Number } 间隔时间
 * @return { Function } 防抖处理的后的方法
 */
export function debounce(delay, fun) {
	let time = null
	return function () {
		let self = this
		if (time) clearTimeout(time)
		time = setTimeout(() => {
			return fun.apply(self, arguments)
		}, delay)
	}
}



/**
 * @description 数组扁平化
 * @param arr { Array } 扁平化对象
 * @return { Array } 扁平化后的数组
 */
export const flatten = (arr) => {
    return arr.reduce((result, item) => {
        return result.concat(Array.isArray(item) ? flatten(item) : item);
    }, []);
}




/**
 * @description 获取不重复的id
 * @param length { Number } id的长度
 * @return { String } id
 */
export const getNonDuplicateID = (length = 8) => {
    let idStr = Date.now().toString(36)
    idStr += Math.random().toString(36).substr(3, length)
    return idStr
}


/**
 * @description 复制到剪切板
 * @param value { String } 复制内容
 * @return { Promise } resolve | reject
 */
export const copyClipboard = (value) => {
    const elInput = document.createElement('input')

    elInput.setAttribute('value', value)
    document.body.appendChild(elInput)
    elInput.select()

    try {
        if (document.execCommand('copy'))
            return Promise.resolve()
        else
            throw new Error()
    } catch (err) {
        return Promise.reject(err)
    } finally {
        document.body.removeChild(elInput)
    }
}


export const objectToQuery = (data) => {
    let _result = []

    for (let key in data) {
        let value = data[key]
        if (Array.isArray(value)) {
            value.forEach(_value => {
                _result.push(key + "=" + _value)
            })
        } else {
            _result.push(key + '=' + value)
        }
    }

    return _result.join('&')
}
