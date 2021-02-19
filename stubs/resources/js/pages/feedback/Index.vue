<template>
    <div>
        <v-dialog v-model="dialog" max-width="800px">
            <v-card raised>
                <v-card-title>
                    <span class="font-semibold text-2xl">{{ formTitle }}</span>
                    <v-spacer></v-spacer>
                    <v-icon color="error" @click="dialog = false">cancel</v-icon>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-2 mt-3">
                        <v-text-field readonly v-model="editedItem.user.uin"
                                      outlined prepend-icon="mdi-dialpad" label="User UIN"
                        ></v-text-field>
                        <v-text-field :disabled="action === 'view'" v-model="editedItem.title"
                                      outlined prepend-icon="title" label="Title"
                        ></v-text-field>
                        <v-textarea class="col-span-2" :disabled="action === 'view'" v-model="editedItem.description" counter
                                    maxlength="2000" outlined prepend-icon="description" label="Description"
                        ></v-textarea>
                    </div>
                </v-card-text>
                <v-divider></v-divider>
                <v-card-actions>
                    <v-spacer></v-spacer>
                    <v-btn outlined color="error" @click="close">Cancel</v-btn>
                    <v-btn :loading="loadingUpdate" color="success" @click="onSubmit(editedItem)">Submit</v-btn>
                </v-card-actions>
            </v-card>
        </v-dialog>
        <v-card raised class="shadow-lg">
            <v-card-title>
                <span class="text-3xl font-semibold">Feedbacks</span>
                <v-spacer></v-spacer>
                <v-btn class="mx-4" @click="addItem" color="primary" outlined><v-icon>mdi-add_circle</v-icon>Add Feedback</v-btn>
                <v-text-field v-model="search" append-icon="search" label="Search" single-line hide-details></v-text-field>
            </v-card-title>
            <v-data-table multi-sort :loading="isLoading" :headers="headers" :search="search" :items="feedbacks" class="elevation-1">
                <template v-slot:item.action="{ item }">
                    <v-icon @click="viewItem(item)" color="primary">mdi-eye</v-icon>
                    <v-icon @click="editItem(item)" color="primary">mdi-pencil</v-icon>
                </template>
            </v-data-table>
        </v-card>
        <v-snackbar v-model="snack" :bottom="true" :right="true" :timeout="3000" :color="snackColor">
            {{ snackText }}
            <v-btn text dark class="" @click="snack = false">Close</v-btn>
        </v-snackbar>
    </div>
</template>

<script>
export default {
    data: () => ({
        isLoading: false,
        snack: false,
        snackColor: '',
        snackText: '',
        feedbacks: [],
        dialog: false,
        appName: process.env.MIX_APP_NAME,
        appUrl: process.env.MIX_APP_URL,
        search: '',
        loadingUpdate: false,
        action: 'view',
        editedIndex: -1,
        editedItem: {
            id: null,
            title: null,
            description: null,
            created_at: null,
            updated_at: null,
            user: {}
        },
        defaultItem: {
            id: null,
            title: null,
            description: null,
            created_at: null,
            updated_at: null,
            user: {}
        },
        headers: [
            {'text': 'UIN', value: 'user.uin'},
            {'text': 'Feedback By', value: 'user.full_name'},
            {'text': 'NetID', value: 'user.name'},
            {'text': 'Feedback Title', value: 'title'},
            {'text': 'Created On', value: 'created_at'},
            {'text': 'Updated On', value: 'updated_at'},
            { text: 'Action', value: 'action', sortable: false },
        ],
    }),
    created() {
        this.fetchFeedbacks();
    },
    computed: {
        formTitle () {
            if(this.action === 'view') return 'View';
            if(this.action === 'add') return 'Add';
            return 'Edit Item';
        },
    },
    watch: {
        dialog (val) {
            val || this.close()
        },
    },
    props: {
        authuser: {
            required: true,
            type: Object
        }
    },
    methods: {
        fetchFeedbacks() {
            this.isLoading = true;
            axios.get(this.appUrl + '/api/feedback')
            .then((response) => {
                if(response.status === 200) {
                    this.feedbacks = response.data;
                }
            }).catch(error => {
                this.snack = true;
                this.snackColor = 'error';
                this.snackText = 'Oops! Something went wrong!'
            }).finally(() => this.isLoading = false);
        },
        addItem() {
            this.action = 'add';
            this.editedItem.user = this.authuser;
            this.dialog = true;
        },
        viewItem (item) {
            this.action = 'view';
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },
        editItem (item) {
            this.action = 'edit';
            this.editedIndex = this.feedbacks.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },
        close () {
            this.dialog = false;
            this.editedItem = Object.assign({}, this.defaultItem);
            this.editedIndex = -1;
        },
        onSubmit(item) {
            if (this.action === 'edit') {
                //Update
                this.loadingUpdate = true;
                axios.post(process.env.MIX_APP_URL + '/api/feedback/' + item.id,{
                    id: item.id,
                    user_uin: item.user.uin,
                    title: item.title,
                    description: item.description,
                    _method: 'patch'
                }).then((response) => {
                    if (response.status === 200) {
                        Object.assign(this.feedbacks[this.editedIndex], response.data);
                        this.snack = true;
                        this.snackColor = 'success';
                        this.snackText = 'Successfully Updated';
                    } else {
                        this.snack = true;
                        this.snackColor = 'error';
                        this.snackText = 'Oops! Something went wrong!';
                    }
                }).catch(error => {
                    this.snack = true;
                    this.snackColor = 'error';
                    this.snackText = 'Oops! Something went wrong!'
                }).finally(() => {
                    this.loadingUpdate = false;
                    this.close();
                });
            } else if(this.action === 'add') {
                //Add
                this.loadingUpdate = true;
                axios.post(process.env.MIX_APP_URL + '/api/feedback',{
                    user_uin: item.user.uin,
                    title: item.title,
                    description: item.description,
                }).then((response) => {
                    if (response.status === 201) {
                        this.feedbacks.push(response.data);
                        this.snack = true;
                        this.snackColor = 'success';
                        this.snackText = 'Successfully Updated';
                    } else {
                        this.snack = true;
                        this.snackColor = 'error';
                        this.snackText = 'Oops! Something went wrong!';
                    }
                }).catch(error => {
                    this.snack = true;
                    this.snackColor = 'error';
                    this.snackText = 'Oops! Something went wrong!'
                }).finally(() => {
                    this.loadingUpdate = false;
                    this.close();
                });
            }
        }
    }
}
</script>

<style scoped>

</style>
