<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <ul class="list-group">
                    <li class="list-group-item d-flex align-items-center" v-for="sprint in sprints">
                        <a
                            href="#"
                            class="flex-grow-1 text-dark"
                            @click.prevent="show(sprint)"
                            v-show="editingSprint.id != sprint.id">
                            {{ sprint.caption }}
                            <small>{{ getDuration(sprint) }}</small>
                        </a>

                        <div class="btn-group" v-show="editingSprint.id != sprint.id">
                            <button
                                class="btn btn-outline-secondary"
                                :disabled="isInEditMode"
                                @click="editSprint(sprint)">Edit</button>

                            <button
                                class="btn btn-outline-secondary"
                                :disabled="isInEditMode"
                                @click="deleteSprint(sprint)">Delete</button>
                        </div>
                        <div class="input-group" v-show="isInEditMode && editingSprint.id == sprint.id">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Sprint Name"
                                v-model="editingSprint.caption">

                            <div class="input-group-append">
                                <button
                                    class="btn btn-outline-secondary"
                                    type="button"
                                    :disabled="editingSprint.caption.length < 3"
                                    @click="saveChanges">Save</button>
                                <button
                                    class="btn btn-outline-secondary"
                                    type="button"
                                    @click="cancelEditing">Cancel</button>
                            </div>
                        </div>

                    </li>
                    <li class="list-group-item text-muted" v-if="!isLoading && sprints.length == 0">
                        No sprints available! Please add one.
                    </li>
                    <li class="list-group-item text-center" v-if="isLoading">
                        Loading ...
                    </li>
                    <li class="list-group-item" v-if="!isLoading">
                        <div class="form-group">
                            <label>Sprint Name</label>
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Sprint Name"
                                :disabled="isInEditMode"
                                v-model="newSprint.caption">
                        </div>

                        <div class="row">
                            <div class="col">
                                <label>Start Date</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    :disabled="isInEditMode"
                                    v-model="newSprint.start">
                            </div>
                            <div class="col">
                                <label>End Date</label>
                                <input
                                    type="date"
                                    class="form-control"
                                    :disabled="isInEditMode"
                                    v-model="newSprint.end">
                            </div>
                        </div>

                        <button
                            class="btn btn-outline-secondary btn-block mt-3"
                            type="button"
                            :disabled="isInEditMode || newSprint.caption.length < 3"
                            @click="createSprint">Add</button>
                    </li>
                </ul>
            </div>
        </div>
    </div>
</template>

<script>
    export default {
        data() {
            return {
                sprints: [],
                newSprint: {
                    caption: '',
                    start: '',
                    end: ''
                },
                editingSprint: {
                    id: -1,
                    caption: '',
                    start: '',
                    end: '',
                },
                isLoading: true
            }
        },
        computed: {
            isInEditMode() {
                return this.editingSprint.id != -1;
            }
        },
        mounted() {
            this.fetchData();
        },
        methods: {
            fetchData() {
                let url = `api/team/${this.$route.params.teamId}/sprint`
                axios.get(url).then(response => {
                    this.sprints = response.data;
                    this.isLoading = false;
                });
            },
            createSprint() {
                let url = `api/team/${this.$route.params.teamId}/sprint`
                let postData = this.newSprint;
                axios.post(url, postData).then(response => {
                    this.sprints.push(response.data);
                    this.newSprint.caption = '';
                    this.newSprint.start = '';
                    this.newSprint.end = '';
                });
            },
            show(sprint) {
                window.router.push({
                    name: 'board.show',
                    params: { sprintId: sprint.id }
                })
            },
            editSprint(sprint) {
                this.editingSprint.id = sprint.id;
                this.editingSprint.caption = sprint.caption;
                this.editingSprint.start = sprint.start;
                this.editingSprint.end = sprint.end;
            },
            cancelEditing() {
                this.editingSprint.id = -1;
                this.editingSprint.caption = '';
                this.editingSprint.start = '';
                this.editingSprint.end = '';
            },
            saveChanges() {
                let putData = {
                    caption: this.editingSprint.caption,
                    start: this.editingSprint.start,
                    end: this.editingSprint.end
                };

                axios.put('api/sprint/' + this.editingSprint.id, putData).then(response => {
                    this.sprints = this.sprints.map(s => {
                        if (s.id == this.editingSprint.id) {
                            return response.data;
                        }

                        return s;
                    });

                    this.cancelEditing();
                });
            },
            deleteSprint(sprint) {
                if (confirm('Are you sure?')) {
                    axios.delete('api/sprint/' + sprint.id).then(response => {
                        this.sprints = this.sprints.filter(t => t.id != sprint.id);
                    });
                }
            },
            getDuration(sprint) {
                return sprint.start.split(' ')[0] + ' - ' + sprint.end.split(' ')[0];
            }
        }
    }
</script>
