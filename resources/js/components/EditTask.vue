<template>
    <center-card>
        <template slot="header">Edit task</template>

        <div class="form-group">
            <label for="nameInput">Name</label>
            <input
                type="text"
                class="form-control"
                id="nameInput"
                v-model="task.caption"
                placeholder="Enter a name for the task">
        </div>
        <div class="form-group">
            <label for="descriptionTextarea">Description</label>
            <textarea
                class="form-control"
                id="descriptionTextarea"
                placeholder="Enter a description for the task"
                v-model="task.description"
                rows="3"></textarea>
        </div>
        <button
            @click="save"
            class="btn btn-primary">Save</button>
        <button
            @click="goToBoard"
            class="btn btn-secondary">Cancel</button>
    </center-card>
</template>

<script>
    export default {
        data() {
            return {
                task: {
                    caption: '',
                    description: '',
                },
                sprintId: -1
            }
        },
        methods: {
            fetchData() {
                let url = `api/task/${this.$route.params.taskId}`;
                axios.get(url).then(response => {
                    this.task = response.data;
                    this.loadStory(this.task.story_id);
                });
            },
            loadStory(storyId) {
                let url = `api/story/${storyId}`;
                axios.get(url).then(response => {
                    this.sprintId = response.data.sprint_id;
                });
            },
            save() {
                let url = `api/task/${this.$route.params.taskId}`;
                let putData = {
                    caption: this.task.caption,
                    description: this.task.description,
                };

                axios.put(url, putData).then(response => {
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
            this.fetchData();
        }
    }
</script>
