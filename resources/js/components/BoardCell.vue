<template>
    <td
        @drop="onDrop($event, state, story)"
        @dragover="onDragOver($event, state, story)"
        @dblclick="addNewTask($event, story)"
        class="pb-1 border-left border-left-dashed">
        <slot></slot>
    </td>
</template>

<script>
    export default {
        props: ['state', 'story', 'tasks', 'draggingTask', 'editMode'],
        methods: {
            onDrop(event, state, story) {
                if (!this.draggingTask) {
                    return;
                }

                let url = `/api/task/${this.draggingTask.id}`;
                let putData = { state_id: state.id, story_id: story.id };
                axios.put(url, putData).then(response => {
                });

                event.preventDefault();
            },
            onDragOver(event, state, story) {
                let activeTask = this.draggingTask;

                if (activeTask && (activeTask.state_id != state.id || activeTask.story_id != story.id)) {
                    event.preventDefault();
                }
            },
            addNewTask(event, story) {
                if (event.target == this.$el && this.editMode) {
                    this.$router.push({
                        name: 'task.create',
                        params: { storyId: story.id }
                    })
                }
            },
        }
    }
</script>
