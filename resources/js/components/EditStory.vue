<template>
    <center-card>
        <template slot="header">Edit story</template>

        <div class="form-group">
            <label for="nameInput">Name</label>
            <input
                type="text"
                class="form-control"
                id="nameInput"
                v-model="story.caption"
                placeholder="Enter a name for the story">
        </div>
        <div class="form-group">
            <label for="descriptionTextarea">Description</label>
            <textarea
                class="form-control"
                id="descriptionTextarea"
                placeholder="Enter a description for the story"
                v-model="story.description"
                rows="3"></textarea>
        </div>
        <div class="form-group">
            <label for="pointsInput">Points</label>
            <input
                type="number"
                class="form-control"
                min="0"
                max="255"
                placeholder="Enter the points for the story"
                v-model="story.points"
                id="pointsInput">
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
                story: {
                    caption: '',
                    description: '',
                    points: '',
                    sprint_id: -1
                }
            }
        },
        methods: {
            fetchData() {
                let url = `api/story/${this.$route.params.storyId}`;
                axios.get(url).then(response => {
                    this.story = response.data;
                });
            },
            save() {
                let url = `api/story/${this.$route.params.storyId}`;
                let putData = {
                    caption: this.story.caption,
                    description: this.story.description,
                    points: this.story.points,
                };

                axios.put(url, putData).then(response => {
                    this.goToBoard();
                });
            },
            goToBoard() {
                window.router.push({
                    name: 'board.show',
                    params: { sprintId: this.story.sprint_id }
                })
            }
        },
        mounted() {
            this.fetchData();
        }
    }
</script>
