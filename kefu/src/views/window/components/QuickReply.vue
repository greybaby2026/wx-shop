<template>
    <div class="quick-reply">
        <el-dialog
            title="快捷回复"
            :visible.sync="visible"
            width="800px"
            top="30vh"
        >
            <el-input
                size="small"
                placeholder="请输入快捷标题关键字搜索"
                prefix-icon="el-icon-search"
                v-model="keyword"
                @keyup.enter.native="getLists"
            >
            </el-input>
            <overlay-scrollbars
                class="scrollbars-contain"
                :options="{
                    scrollbars: { autoHide: 'scroll' },
                    overflowBehavior: { x: 'hidden' },
                    callbacks: { onScroll: handleScroll },
                }"
            >
                <div class="reply-lists" v-if="replyLists.length">
                    <div
                        class="reply-item"
                        v-for="(item, index) in replyLists"
                        :key="index"
                        @click.stop="handleSelect(item)"
                    >
                        <div class="reply-item__title weight-500">
                            {{ item.title }}
                        </div>
                        <div class="reply-item__content muted m-t-5">
                            {{ item.content }}
                        </div>
                    </div>
                </div>
                <el-empty v-else :image-size="100"></el-empty>
            </overlay-scrollbars>

			<el-pagination
			    layout="prev, pager, next"
			    :total="pagination.total"
				:current-page.sync="pagination.page"
				@current-change="getLists()" 
				style="text-align: center"
				v-if="pagination.total*1"
			>
			  </el-pagination>
        </el-dialog>
    </div>
</template>


<script>
import { apiReplyList } from '@/api/app'
export default {
    name: 'QuickReply',

    props: {
        value: {
            type: Boolean,
            default: false,
        },
    },

    data() {
        return {
            replyLists: [],
            keyword: '',
			
			/* 分页器 */
			pagination: {
				size: 15,
				page: 1,
				total: 0,
			},
        }
    },
    computed: {
        visible: {
            get() {
                return this.value
            },
            set(val) {
                this.$emit('input', val)
            },
        },
    },
    watch: {
        visible(val) {
            if (val) this.getLists()
        }
    },
    created() {},

    methods: {
        handleScroll() {},
        // 获取快捷回复列表
        getLists() {
			let { size, page, total} = this.pagination
            apiReplyList({
                title: this.keyword,
				page_no: page,
				page_size: size
            }).then((res) => {
				// console.log('getLists', res)
                this.replyLists = res.lists
            })
        },
        handleSelect(item) {
            this.$emit('select', item.content)
            this.visible = false
        },
    },
}
</script>


<style lang="scss" scoped>
.quick-reply {
    .scrollbars-contain {
        height: 400px;
        .reply-lists {
            padding: 10px 0;
            .reply-item {
                border-radius: 4px;
                background: #f9f9f9;
                padding: 10px 16px;
                font-size: 12px;
                color: #333;
                cursor: pointer;
                &:not(:last-of-type) {
                    margin-bottom: 10px;
                }
            }
        }
    }
}
</style>
