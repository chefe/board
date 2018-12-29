<template>
    <div class="container">
        <div class="row justify-content-center">
            <table class="table">
                <thead>
                    <tr>
                        <th :width="tableColWidth">STORIES</th>
                        <th
                            v-for="state in states"
                            :width="tableColWidth"
                            v-text="state.caption.toUpperCase()"></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="story in stories">
                        <td>
                            <strong>{{ story.caption }}</strong>
                            <span class="badge badge-secondary">{{ story.points }}</span>
                            <p v-text="story.description"></p>
                        </td>
                        <td
                            v-for="state in states"
                            @drop="onDrop($event, state)"
                            @dragover="onDragOver($event, state, story)"
                            class="pb-1">

                            <div
                                class="card mb-2"
                                :draggable="true"
                                @dragstart="onDragStart($event, task)"
                                v-for="task in getTasksForState(story, state)">

                                <div class="card-body p-2">
                                    <h5
                                        class="card-title mb-0"
                                        v-text="task.caption"></h5>
                                    <p
                                        class="card-text mt-2"
                                        v-show="showTaskDescription && task.description"
                                        v-text="task.description"></p>
                                </div>
                            </div>

                            <button
                                v-if="state.id == states[0].id"
                                @click="addNewTask(story)"
                                class="btn btn-block btn-light text-muted text-left rounded-0">
                                Add a new task
                            </button>
                        </td>
                    </tr>
                    <tr>
                        <td :colspan="states.length + 1" class="p-0">
                            <button
                                @click="addNewStory"
                                class="btn btn-block btn-light p-3 text-muted rounded-0">
                                Add a new story
                            </button>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                showTaskDescription: false,
                states: [],
                stories: [],
                tasks: []
            }
        },
        computed: {
            tableColWidth() {
                return 1 / (this.states.length + 1) * 100 + '%';
            }
        },
        methods: {
            fetchData() {
                this.loadStates();
                this.loadStories();
            },
            loadStates() {
                axios.get('api/task/state').then(response => {
                    this.states = response.data;
                });
            },
            loadStories() {
                let url = `api/sprint/${this.$route.params.sprintId}/story`;
                axios.get(url).then(response => {
                    this.stories = response.data;
                    this.tasks = [];
                    this.stories.forEach(s => {
                        this.loadTasks(s);
                    });
                });
            },
            loadTasks(story) {
                let url = `api/story/${story.id}/task`;
                axios.get(url).then(response => {
                    response.data.forEach(t => {
                        this.tasks.push(t);
                    });
                });
            },
            getTasksForState(story, state) {
                return this.tasks.filter(t => {
                    return t.story_id == story.id && t.state_id == state.id;
                });
            },
            onDragStart: function(event, task) {
                event.dataTransfer.dropEffect = 'move';
                event.dataTransfer.setData('text/plain', task.id);
            },
            onDrop(event, state) {
                let taskId = event.dataTransfer.getData('text');
                let activeTask = this.tasks.filter(t => t.id == taskId)[0];
                let url = `api/task/${taskId}`;
                let putData = {
                    caption: activeTask.caption,
                    description: activeTask.description,
                    state_id: state.id
                };

                axios.put(url, putData).then(response => {
                    this.tasks = this.tasks.map(t => {
                        if (t.id == taskId) {
                            return response.data;
                        }
                        return t;
                    });
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
            addNewStory() {
                window.router.push({
                    name: 'story.create',
                    params: { sprintId: this.$route.params.sprintId }
                })
            },
            addNewTask(story) {
                window.router.push({
                    name: 'task.create',
                    params: { storyId: story.id }
                })
            }
        },
        mounted() {
            this.fetchData();
        }
    }
</script>
