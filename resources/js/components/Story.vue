<template>
    <div>
        <div class="d-flex">
            <div class="flex-grow-1">
                <strong>{{ story.caption }}</strong>
                <span class="badge badge-secondary">{{ story.points }}</span>
            </div>
            <button class="btn btn-sm btn-light" @click="editStory(story)">
                <svg class="icon" viewBox="0 0 20 20">
                    <path fill="currentColor" d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"/>
                </svg>
            </button>
            <button class="btn btn-sm btn-light" @click="deleteStory(story)">
                <svg class="icon" viewBox="0 0 20 20">
                    <path fill="currentColor" d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
                </svg>
            </button>
        </div>
        <p v-text="story.description"></p>

        <button
            @click="addNewTask(story)"
            class="btn btn-block btn-light text-muted rounded-0 border border-dashed">
            Add a new task
        </button>
    </div>
</template>

<script>
    export default {
        props: ['story'],
        methods: {
            addNewTask(story) {
                this.$router.push({
                    name: 'task.create',
                    params: { storyId: story.id }
                })
            },
            editStory(story) {
                this.$router.push({
                    name: 'story.edit',
                    params: { storyId: story.id }
                });
            },
            deleteStory(story) {
                if (confirm('Are you sure?')) {
                    axios.delete('/api/story/' + story.id).then(request => {
                        this.$emit('deleteStory', story.id);
                    });
                }
            },
        }
    }
</script>
