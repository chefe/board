<template>
    <div
        class="card mb-2"
        :draggable="true"
        @dragstart="onDragStart($event, task)"
        @dragend="onDragEnd($event)">

        <div
            class="card-header d-flex p-2 align-items-center"
            :class="{ 'border-bottom-0': !showTaskDescription || !task.description }">
            <h5 class="mb-0 flex-grow-1" v-text="task.caption"></h5>
            <button class="btn btn-sm btn-light" @click="editTask(task)" v-if="editMode">
                <svg class="icon" viewBox="0 0 20 20">
                    <path fill="currentColor" d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"/>
                </svg>
            </button>
            <button class="btn btn-sm btn-light" @click="deleteTask(task)" v-if="editMode">
                <svg class="icon" viewBox="0 0 20 20">
                    <path fill="currentColor" d="M6 2l2-2h4l2 2h4v2H2V2h4zM3 6h14l-1 14H4L3 6zm5 2v10h1V8H8zm3 0v10h1V8h-1z"/>
                </svg>
            </button>
        </div>

        <div
            class="card-body p-2"
            v-if="showTaskDescription && task.description">
            <p
                class="card-text mt-2"
                v-text="task.description"></p>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['task', 'showTaskDescription', 'editMode'],
        methods: {
            onDragStart(event, task) {
                event.dataTransfer.dropEffect = 'move';
                event.dataTransfer.setData('text/plain', task.id);
                this.$emit('begin-dragging', task);
            },
            onDragEnd(event) {
                this.$emit('end-dragging');
            },
            editTask(task) {
                this.$router.push({
                    name: 'task.edit',
                    params: { taskId: task.id }
                });
            },
            deleteTask(task) {
                if (confirm('Are you sure?')) {
                    axios.delete('/api/task/' + task.id).then(request => {
                    });
                }
            }
        }
    }
</script>
