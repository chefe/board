<template>
    <div class="container">
        <div class="row">
            <div class="col">
                <ul class="list-group">
                    <li class="list-group-item d-flex align-items-center" v-for="team in teams">
                        <a href="#"
                            class="flex-grow-1 text-dark"
                            @click.prevent="show(team)"
                            v-text="team.caption"
                            v-show="editingTeamId != team.id"></a>

                        <div class="btn-group" v-show="editingTeamId != team.id">
                            <button
                                class="btn btn-outline-secondary"
                                :disabled="isInEditMode"
                                @click="editTeam(team)">Edit</button>

                            <button
                                class="btn btn-outline-secondary"
                                :disabled="isInEditMode"
                                @click="deleteTeam(team)">Delete</button>
                        </div>
                        <div class="input-group" v-show="isInEditMode && editingTeamId == team.id">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Team Name"
                                v-model="editingTeamCaption">

                            <div class="input-group-append">
                                <button
                                    class="btn btn-outline-secondary"
                                    type="button"
                                    :disabled="editingTeamCaption.length < 3"
                                    @click="saveChanges">Save</button>
                                <button
                                    class="btn btn-outline-secondary"
                                    type="button"
                                    @click="cancelEditing">Cancel</button>
                            </div>
                        </div>

                    </li>
                    <li class="list-group-item text-muted" v-if="!isLoading && teams.length == 0">
                        No teams available! Please add one.
                    </li>
                    <li class="list-group-item text-center" v-if="isLoading">
                        Loading ...
                    </li>
                    <li class="list-group-item" v-if="!isLoading">
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                placeholder="Team Name"
                                :disabled="isInEditMode"
                                v-model="newTeamName">

                            <div class="input-group-append">
                                <button
                                    class="btn btn-outline-secondary"
                                    type="button"
                                    :disabled="isInEditMode || newTeamName.length < 3"
                                    @click="createTeam">Add</button>
                            </div>
                        </div>
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
                teams: [],
                newTeamName: '',
                editingTeamId: -1,
                editingTeamCaption: '',
                isLoading: true
            }
        },
        computed: {
            isInEditMode() {
                return this.editingTeamId != -1;
            }
        },
        mounted() {
            this.fetchData();
        },
        methods: {
            show(team) {
                window.router.push({
                    name: 'sprint.index',
                    params: { teamId: team.id }
                })
            },
            fetchData() {
                axios.get('api/team').then(response => {
                    this.teams = response.data;
                    this.isLoading = false;
                });
            },
            createTeam() {
                axios.post('api/team', { caption: this.newTeamName }).then(response => {
                    this.teams.push(response.data);
                    this.newTeamName = '';
                });
            },
            editTeam(team) {
                this.editingTeamId = team.id;
                this.editingTeamCaption = team.caption;
            },
            cancelEditing() {
                this.editingTeamId = -1;
            },
            saveChanges() {
                let putData = { caption: this.editingTeamCaption };
                axios.put('api/team/' + this.editingTeamId, putData).then(response => {
                    this.teams = this.teams.map(t => {
                        if (t.id == this.editingTeamId) {
                            return response.data;
                        }

                        return t;
                    });

                    this.editingTeamId = -1;
                    this.editingTeamCaption = '';
                });
            },
            deleteTeam(team) {
                if (confirm('Are you sure?')) {
                    axios.delete('api/team/' + team.id).then(response => {
                        this.teams = this.teams.filter(t => t.id != team.id);
                    });
                }
            }
        }
    }
</script>
