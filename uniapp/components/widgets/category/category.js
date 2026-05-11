export default {
    data() {
        return {
            isCreated: false
        }
    },
    computed: {
        lists() {
            return this.isCreated ? this.parent.lists : []
        },
        leftlists() {
            let arr = []
            if (
                this.isCreated &&
                this.parent.lists.length &&
                this.parent.lists[this.selectIndex].sons
            ) {
                arr = [{ id: this.parent.lists[this.selectIndex].id, name: '全部' }]
                this.parent.lists[this.selectIndex].sons.forEach((item) => {
                    arr.push(item)
                })
            }
            return arr
        },
        thirdlyLists() {
            let arr = []
            if (this.leftlists[this.leftIndex] && this.leftlists[this.leftIndex].sons) {
                arr = [{ id: this.leftlists[this.leftIndex].id, name: '全部' }]
                this.leftlists[this.leftIndex].sons.forEach((item) => {
                    arr.push(item)
                })
            }
            return arr
        },
        selectIndex() {
            return this.isCreated ? this.parent.selectIndex : -1
        },
        leftIndex() {
            return this.isCreated ? this.parent.leftIndex : -1
        },
        thirdlyIndex() {
            return this.isCreated ? this.parent.thirdlyIndex : -1
        },
        banner() {
            return this.isCreated ? this.parent.banner : null
        },
        height() {
            return this.isCreated ? this.parent.height : 0
        },
        tabbarHeight() {
            return this.isCreated ? this.parent.tabbarHeight : 0
        }
    },
    created() {
        Object.defineProperty(this, 'parent', {
            get: () => this.$getParent.call(this, 'category') || {}
        })
        this.isCreated = true
    }
}
