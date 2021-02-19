<template>
    <div>
        <v-dialog v-model="dialog" max-width="800px">
            <v-card raised>
                <v-card-title>
                    <span class="font-semibold text-2xl">Add New User</span>
                    <v-spacer></v-spacer>
                    <v-icon color="error" @click="dialog = false">cancel</v-icon>
                </v-card-title>
                <v-divider></v-divider>
                <v-card-text>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-2 mt-3">
                        <v-text-field v-model="editedItem.first_name" outlined prepend-icon="person" label="User First Name" required></v-text-field>
                        <v-text-field v-model="editedItem.last_name" outlined prepend-icon="person" label="User Last Name" required></v-text-field>
                        <v-text-field v-model="editedItem.uin" outlined prepend-icon="mdi-dialpad" label="User UIN" required></v-text-field>
                        <v-text-field v-model="editedItem.name" outlined prepend-icon="mdi-details" label="User NetID" required></v-text-field>
                        <v-text-field v-model="editedItem.email" outlined prepend-icon="mdi-email" label="User Email" required></v-text-field>
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
                <span class="text-3xl font-semibold">Users</span>
                <v-spacer></v-spacer>
                <v-btn class="mx-4" @click="dialog = true" color="primary" outlined><v-icon>mdi-person_add</v-icon>Add User</v-btn>
                <v-text-field v-model="search" append-icon="search" label="Search" single-line hide-details></v-text-field>
            </v-card-title>
            <v-data-table multi-sort :loading="loadingUsers" :headers="headers" :search="search" :items="users" class="elevation-1">
                <template v-slot:item.action="{ item }">
                    <v-icon @click="editItem(item)" color="primary">mdi-pencil</v-icon>
                    <v-btn :href="appUrl + '/user/' + item.id + '/impersonate'" class="ma-2" outlined color="primary">
                        <v-icon left>mdi-check</v-icon>Impersonate
                    </v-btn>
                </template>
            </v-data-table>
        </v-card>
    </div>
</template>
<script>
export default {
    data: () => ({
        loadingUsers: false,
        dialog: false,
        appName: process.env.MIX_APP_NAME,
        appUrl: process.env.MIX_APP_URL,
        csrfToken: document.querySelector('meta[name="csrf-token"]').content,
        search: '',
        loadingUpdate: false,
        editedIndex: -1,
        editedItem: {
            first_name: null,
            last_name: null,
            name: null,
            email: null,
            uin: null,
        },
        defaultItem: {
            first_name: null,
            last_name: null,
            name: null,
            email: null,
            uin: null,
        },
        headers: [
            {'text': 'UIN', value: 'uin'},
            {'text': 'First Name', value: 'first_name'},
            {'text': 'Last Name', value: 'last_name'},
            {'text': 'NetID', value: 'name'},
            { text: 'Action', value: 'action', sortable: false },
        ],
    }),
    props:{
        users:{
            type: Array,
            required: true,
        },
    },
    computed: {
        formTitle () {
            return this.editedIndex === -1 ? 'New Item' : 'Edit Item'
        },
    },
    watch: {
        dialog (val) {
            val || this.close()
        },
    },
    created(){
    },
    methods: {
        editItem (item) {
            this.editedIndex = this.users.indexOf(item);
            this.editedItem = Object.assign({}, item);
            this.dialog = true;
        },
        close () {
            this.dialog = false;
            this.editedItem = Object.assign({}, this.defaultItem);
            this.editedIndex = -1;
        },
        onSubmit(item) {
            if (this.editedIndex > -1) {
                //Update
                this.loadingUpdate = true;
                axios.post(process.env.MIX_APP_URL + '/api/user/' + item.id,{
                    name: item.name,
                    first_name: item.first_name,
                    last_name: item.last_name,
                    uin: item.uin,
                    email: item.email,
                    _method: 'patch'
                }).then((response) => {
                    if (response.status === 200) {
                        Object.assign(this.users[this.editedIndex], response.data);
                        this.snack = true;
                        this.snackColor = 'success';
                        this.snackText = 'Successfully Updated';
                    } else {
                        this.snack = true;
                        this.snackColor = 'error';
                        this.snackText = 'Oops! Something went wrong!';
                    }
                    this.close();
                }).catch(error => {
                    this.snack = true;
                    this.snackColor = 'error';
                    this.snackText = 'Oops! Something went wrong!'
                }).finally(() => {
                    this.loadingUpdate = false;
                });
            } else {
                //Add
                this.loadingUpdate = true;
                axios.post(process.env.MIX_APP_URL + '/api/user',{
                    name: item.name,
                    first_name: item.first_name,
                    last_name: item.last_name,
                    uin: item.uin,
                    email: item.email,
                }).then((response) => {
                    if (response.status === 200) {
                        this.users.push(response.data);
                        this.snack = true;
                        this.snackColor = 'success';
                        this.snackText = 'Successfully Updated';
                    } else {
                        this.snack = true;
                        this.snackColor = 'error';
                        this.snackText = 'Oops! Something went wrong!';
                    }
                    this.close();
                }).catch(error => {
                    this.snack = true;
                    this.snackColor = 'error';
                    this.snackText = 'Oops! Something went wrong!'
                }).finally(() => {
                    this.loadingUpdate = false;
                });
            }
        }
    }
}
</script>
