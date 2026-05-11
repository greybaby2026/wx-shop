/**
 * @param {string} path
 * @returns {Boolean}
 */
export function isExternalLink(path) {
    return /^(https?:|mailto:|tel:)/.test(path)
}

export function isObject(val) {
    return val !== null && typeof val === 'object'
}

export function isString(val) {
    return typeof val === 'string'
}