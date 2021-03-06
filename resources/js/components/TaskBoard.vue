<template>
    <div class="px-5">
        <div class="row mb-4">
            <div class="col d-flex">
                <nav class="flex-grow-1">
                    <ol class="breadcrumb bg-transparent px-0 mb-0">
                        <li class="breadcrumb-item">
                            <router-link
                                v-text="team.caption"
                                :to="{ name: 'sprint.index', params: { teamId: team.id }}"></router-link>
                        </li>
                        <li class="breadcrumb-item active">
                            {{ sprint.caption}}
                            <small class="ml-2" v-text="sprintDuration"></small>
                        </li>
                    </ol>
                </nav>
                <div class="btn-group">
                    <button
                        class="btn btn-outline-dark"
                        @click="detailMode = !detailMode"
                        :class="{ 'active': detailMode}">Show Details</button>
                    <button
                        class="btn btn-outline-dark"
                        @click="toggleFullscreen"
                        :class="{ 'active': fullscreenMode }">Fullscreen Mode</button>
                    <button
                        class="btn btn-outline-dark"
                        @click="editMode = !editMode"
                        :class="{ 'active': editMode }">Edit Mode</button>
                </div>
            </div>
        </div>
        <div class="row justify-content-center">
            <div class="col">
                <table class="table">
                    <thead>
                        <tr>
                            <th :width="tableColWidth">STORIES</th>
                            <th v-for="state in states" :width="tableColWidth">
                                {{ state.caption.toUpperCase() }}
                                <span class="badge badge-secondary">{{ getTaskCountForState(state) }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="story in stories">
                            <td>
                                <story
                                    :story="story"
                                    :editMode="editMode"
                                    @deleteStory="deleteStory"></story>
                            </td>
                            <board-cell
                                v-for="state in states"
                                :state="state"
                                :story="story"
                                :editMode="editMode"
                                :dragging-task="draggingTask"
                                :key="'cell' + story.id + '-' + state.id">
                                <task
                                    v-for="task in getTasksForState(story, state)"
                                    :showTaskDescription="detailMode"
                                    :editMode="editMode"
                                    :key="'task-' + task.id"
                                    :task="task"
                                    @begin-dragging="onBeginDragging"
                                    @end-dragging="onEndDragging"></task>
                            </board-cell>
                        </tr>
                        <tr v-if="editMode">
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
                detailMode: false,
                fullscreenMode: false,
                editMode: false,
                draggingTask: undefined,
                sprint: {
                    caption: '',
                    start: '',
                    end: ''
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
        watch: {
            detailMode: function (newMode, oldMode) {
                localStorage.detailMode = newMode;
            },
            editMode: function (newMode, oldMode) {
                localStorage.editMode = newMode;
            },
        },
        computed: {
            tableColWidth() {
                return 1 / (this.states.length + 1) * 100 + '%';
            },
            sprintDuration() {
                return this.sprint.start.split(' ')[0] + ' - ' + this.sprint.end.split(' ')[0];
            }
        },
        methods: {
            loadSettings() {
                if (localStorage.detailMode != undefined) {
                    this.detailMode = (localStorage.detailMode == "true");
                }
                if (localStorage.editMode != undefined) {
                    this.editMode = (localStorage.editMode == "true");
                }
            },
            fetchData() {
                let url = `/api/sprint/${this.$route.params.sprintId}/board`;
                axios.get(url).then(response => {
                    this.sprint = response.data.sprint;
                    this.team = response.data.team;
                    this.stories = response.data.stories;
                    this.tasks = response.data.tasks;
                    this.states = response.data.states;
                    this.setupWebsockets();
                });
            },
            setupWebsockets() {
                Echo.channel('board.' + this.sprint.id).listen('.task.updated', (e) => {
                    this.updateTask(e.task);
                }).listen('.task.created', (e) => {
                    this.tasks.push(e.task);
                }).listen('.task.deleted', (e) => {
                    this.tasks = this.tasks.filter(t => t.id != e.task.id);
                }).listen('.story.updated', (e) => {
                    this.updateStory(e.story);
                }).listen('.story.created', (e) => {
                    this.stories.push(e.story);
                }).listen('.story.deleted', (e) => {
                    this.deleteStory(e.story.id);
                });
            },
            getTasksForState(story, state) {
                return this.tasks.filter(t => {
                    return t.story_id == story.id && t.state_id == state.id;
                });
            },
            getTaskCountForState(state) {
                return this.tasks.filter(t => {
                    return t.state_id == state.id;
                }).length;
            },
            onBeginDragging(task) {
                this.draggingTask = task;
            },
            onEndDragging() {
                this.draggingTask = undefined;
            },
            updateTask(task) {
                this.tasks = this.tasks.map(t => {
                    if (t.id == task.id) {
                        return task;
                    }

                    return t;
                });
            },
            updateStory(story) {
                this.stories = this.stories.map(s => {
                    if (s.id == story.id) {
                        return story;
                    }

                    return s;
                });
            },
            addNewStory() {
                window.router.push({
                    name: 'story.create',
                    params: { sprintId: this.$route.params.sprintId }
                })
            },
            deleteStory(storyId) {
                this.stories = this.stories.filter(s => s.id != storyId);
                this.tasks = this.tasks.filter(t => t.story_id != storyId);
            },
            toggleFullscreen() {
                if (this.fullscreenMode) {
                    this.exitFullscreen();
                } else {
                    this.enterFullscreen();
                }
            },
            enterFullscreen() {
                let element = document.querySelector('main');

                if (element.requestFullScreen) {
                    element.requestFullScreen();
                } else if (element.webkitRequestFullScreen ) {
                    element.webkitRequestFullScreen(Element.ALLOW_KEYBOARD_INPUT);
                } else if (element.mozRequestFullScreen) {
                    element.mozRequestFullScreen();
                } else if (element.msRequestFullscreen) {
                    element.msRequestFullscreen();
                }
            },
            exitFullscreen() {
                if (document.exitFullscreen) {
                    document.exitFullscreen();
                } else if (document.msExitFullscreen) {
                    document.msExitFullscreen();
                } else if (document.mozCancelFullScreen) {
                    document.mozCancelFullScreen();
                } else if (document.webkitExitFullscreen) {
                    document.webkitExitFullscreen();
                }
            },
            fullscreenChanged() {
                this.fullscreenMode = Boolean(document.fullscreen)
                    || Boolean(document.mozFullScreen)
                    || Boolean(document.webkitFullScreen)
                    || Boolean(document.msRequestFullscreen);
            },
            addFullscreenChangedListener() {
                document.addEventListener('fullscreenchange', this.fullscreenChanged)
                document.addEventListener('mozfullscreenchange', this.fullscreenChanged)
                document.addEventListener('MSFullscreenChange', this.fullscreenChanged)
                document.addEventListener('webkitfullscreenchange', this.fullscreenChanged)
            },
            removeFullscreenChangedListener() {
                document.removeEventListener('fullscreenchange', this.fullscreenChanged)
                document.removeEventListener('mozfullscreenchange', this.fullscreenChanged)
                document.removeEventListener('MSFullscreenChange', this.fullscreenChanged)
                document.removeEventListener('webkitfullscreenchange', this.fullscreenChanged)
            }
        },
        mounted() {
            this.loadSettings();
            this.fetchData();
            this.addFullscreenChangedListener();
        },
        beforeDestroy() {
            this.removeFullscreenChangedListener();
        }
    }
</script>
