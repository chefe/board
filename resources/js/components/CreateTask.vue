<template>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">
                <div class="card card-default">
                    <div class="card-header">Create a task</div>

                    <div class="card-body">
                        <div class="form-group">
                            <label for="nameInput">Name</label>
                            <input
                                type="text"
                                class="form-control"
                                id="nameInput"
                                v-model="caption"
                                placeholder="Enter a name for the story">
                        </div>
                        <div class="form-group">
                            <label for="descriptionTextarea">Description</label>
                            <textarea
                                class="form-control"
                                id="descriptionTextarea"
                                placeholder="Enter a description for the story"
                                v-model="description"
                                rows="3"></textarea>
                        </div>
                        <button
                            @click="createTask"
                            class="btn btn-primary">Create</button>
                        <button
                            @click="goToBoard"
                            class="btn btn-secondary">Cancel</button>
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
                caption: '',
                description: '',
                sprintId: ''
            }
        },
        methods: {
            createTask() {
                let url = `api/story/${this.$route.params.storyId}/task`;
                let postData = {
                    caption: this.caption,
                    description: this.description,
                };

                axios.post(url, postData).then(response => {
                    this.goToBoard();
                });
            },
            goToBoard() {
                window.router.push({
                    name: 'board.show',
                    params: { sprintId: this.sprintId }
                })
            }
        },
        mounted() {
            let url = `api/story/${this.$route.params.storyId}`;
            axios.get(url).then(response => {
                this.sprintId = response.data.sprint_id;
            });
        }
    }
</script>
