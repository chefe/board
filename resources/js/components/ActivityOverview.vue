<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="text-center">
                    <h1 class="display-4 mb-4">Activities</h1>
                    <hr width="250px">
                    <a
                        :href="page > 1 ? '#' : undefined"
                        @click.prevent="goToPage(page - 1)">Previous</a>

                    &mdash; Page {{ page }} &mdash;

                    <a
                        :href="page < pagination.last_page ? '#' : undefined"
                        @click.prevent="goToPage(page + 1)">Next</a>
                </div>
                <div v-for="activity in  pagination.data" class="media mb-2">
                    <div class="bg-secondary text-white rounded d-flex align-items-center justify-content-center mr-3 mt-2"
                        style="font-size:150%;width:2em;height:2em">
                        <svg class="icon" viewBox="0 0 20 20"><path fill="currentColor" d="M7.03 2.6a3 3 0 0 1 5.94 0L15 3v1h1a2 2 0 0 1 2 2v12a2 2 0 0 1-2 2H4a2 2 0 0 1-2-2V6c0-1.1.9-2 2-2h1V3l2.03-.4zM5 6H4v12h12V6h-1v1H5V6zm5-2a1 1 0 1 0 0-2 1 1 0 0 0 0 2z"/></svg>
                    </div>
                    <div class="media-body">
                        <small v-text="activity.date" class="text-muted font-weight-light"></small>
                        <h5>
                            {{ ucword(activity.description) }}
                            {{ activity.subject_type }}
                            #{{ activity.subject_properties.id }}
                        </h5>
                        <ul v-if="activity.description == 'updated'" class="list-unstyled">
                            <li v-for="(change, key) in activity.changes">
                                Changed <code>{{ key }}</code>
                                from <code>{{ change.old }}</code> to <code>{{ change.new }}</code>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                page: -1,
                pagination: {
                    data: []
                }
            };
        },
        computed: {
            prevPageUrl() {
                if (this.pagination && this.pagination.prev_page_url) {
                    return '/activity?page=' + (this.page - 1);
                }

                return undefined;
            },
            nextPageUrl() {
                if (this.pagination && this.pagination.next_page_url) {
                    return '/activity?page=' + (this.page + 1);
                }

                return undefined;
            }
        },
        methods: {
            goToPage(p) {
                if (p < 1 || (this.pagination && p > this.pagination.last_page)) {
                    return;
                }

                let url = `/api/activity?page=${p}`;

                axios.get(url).then(response => {
                    this.pagination = response.data;
                    this.page = p;

                    let url = '/activity?page=' + p;
                    this.$router.push(url);
                });
            },
            ucword(value) {
                return value.replace(/\w+/g, a => {
                    return a.charAt(0).toUpperCase() + a.slice(1).toLowerCase();
                });
            },
        },
        mounted() {
            let page = this.$route.query.page;
            this.goToPage(page ? parseInt(page) : 1);
        },
    }
</script>
