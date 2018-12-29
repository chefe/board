<template>
    <td
        @drop="onDrop($event, state)"
        @dragover="onDragOver($event, state, story)"
        class="pb-1">
        <slot></slot>
    </td>
</template>

<script>
    export default {
        props: ['state', 'story', 'tasks'],
        methods: {
            onDrop(event, state) {
                let taskId = event.dataTransfer.getData('text');
                let url = `/api/task/${taskId}`;
                let putData = { state_id: state.id };

                axios.put(url, putData).then(response => {
                    this.$emit('updateTask', response.data);
                });

                event.preventDefault();
            },
            onDragOver(event, state, story) {
                let taskId = event.dataTransfer.getData('text');
                let activeTask = this.tasks.filter(t => t.id == taskId)[0];

                if (activeTask.state_id != state.id && activeTask.story_id == story.id) {
                    event.preventDefault();
                }
            },
        }
    }
</script>
