export interface FileAddCate_Req {
    type: 10 | 20 | 30
    pid: number
    name: string
}

export interface FileEditCate_Req {
    id: number
    name: string
}
