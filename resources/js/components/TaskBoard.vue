<template>
    <div :class="{ 'container': !fullscreenMode, 'px-5': fullscreenMode }">
        <div class="row mb-4">
            <div class="col d-flex">
                <nav class="flex-grow-1">
                    <ol class="breadcrumb bg-transparent px-0 mb-0">
                        <li class="breadcrumb-item">
                            <router-link
                                v-text="team.caption"
                                :to="{ name: 'sprint.index', params: { teamId: team.id }}"></router-link>
                        </li>
                        <li class="breadcrumb-item active" v-text="sprint.caption"></li>
                    </ol>
                </nav>
                <div class="btn-group">
                    <button
                        class="btn btn-outline-dark"
                        @click="showTaskDescription = !showTaskDescription"
                        :class="{ 'active': showTaskDescription}">Show Details</button>
                    <button
                        class="btn btn-outline-dark"
                        @click="fullscreenMode = !fullscreenMode"
                        :class="{ 'active': fullscreenMode }">Fullscreen Mode</button>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col">
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

                                    <div
                                        class="card-header d-flex p-2 align-items-center"
                                         :class="{ 'border-bottom-0': !showTaskDescription || !task.description }">
                                        <h5 class="mb-0 flex-grow-1" v-text="task.caption"></h5>
                                        <button class="btn btn-sm btn-light" @click="editTask(task)">
                                            <svg class="icon" viewBox="0 0 20 20">
                                                <path fill="currentColor" d="M12.3 3.7l4 4L4 20H0v-4L12.3 3.7zm1.4-1.4L16 0l4 4-2.3 2.3-4-4z"/>
                                            </svg>
                                        </button>
                                        <button class="btn btn-sm btn-light" @click="deleteTask(task)">
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
                            </td>
                        </tr>
                        <tr>
                            <td :colspan="states.length + 1" class="p-0 pt-1">
                                <button
                                    @click="addNewStory"
                                    class="btn btn-block btn-light p-3 text-muted rounded-0 border border-dashed">
                                    Add a new story
                                </button>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                showTaskDescription: false,
                fullscreenMode: false,
                sprint: {
                    caption: ''
                },
                team: {
                    id: -1,
                    caption: ''
                },
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
                this.loadSprint();
                this.loadStories();
            },
            loadStates() {
                axios.get('/api/task/state').then(response => {
                    this.states = response.data;
                });
            },
            loadSprint() {
                let url = `/api/sprint/${this.$route.params.sprintId}`;
                axios.get(url).then(response => {
                    this.sprint = response.data;
                    this.loadTeam(this.sprint.team_id);
                });
            },
            loadTeam(teamId) {
                let url = `/api/team/${teamId}`;
                axios.get(url).then(response => {
                    this.team = response.data;
                });
            },
            loadStories() {
                let url = `/api/sprint/${this.$route.params.sprintId}/story`;
                axios.get(url).then(response => {
                    this.stories = response.data;
                    this.tasks = [];
                    this.stories.forEach(s => {
                        this.loadTasks(s);
                    });
                });
            },
            loadTasks(story) {
                let url = `/api/story/${story.id}/task`;
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
                let url = `/api/task/${taskId}`;
                let putData = { state_id: state.id };

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
                        this.stories = this.stories.filter(s => s.id != story.id);
                        this.tasks = this.tasks.filter(t => t.story_id != story.id);
                    });
                }
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
                        this.tasks = this.tasks.filter(t => t.id != task.id);
                    });
                }
            }
        },
        mounted() {
            this.fetchData();
        }
    }
</script>
