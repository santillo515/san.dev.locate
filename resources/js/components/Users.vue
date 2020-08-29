<template>
    <div class="container">
        <div class="row mt-5" v-if="$gate.isAdminOrAuthor()">
          <div class="col-md-12">
            <div class="card">
              <div class="card-header">
                <h3 class="card-title">Таблица пользователей</h3>

                <div class="card-tools">
                    <button class="btn btn-success" @click="newModal">Добавить <i class="fas fa-user-plus fa-fw"></i></button>
                </div>
              </div>
              <!-- /.card-header -->
              <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                  <tbody>
                    <tr>
                      <th>ID</th>
                      <th>Имя</th>
                      <th>Е-майл</th>
                      <th>Роль</th>
					  <th>Дата рег-ции</th>
                      <th>Изменить</th>
                    </tr>
                    <tr v-for="user in users.data" :key="user.id">
                      <td>{{user.id}}</td>
                      <td>{{user.name}}</td>
                      <td>{{user.email}}</td>
					  <td>{{user.type | upText}}</td>
					  <td>{{user.created_at | myDate}}</td>
                      <td>
                          <a href="#" @click="editModal(user)">
                              <i class="fa fa-edit blue"></i>
                          </a>
                          /
                          <a href="#" @click="deleteUser(user.id)">
                              <i class="fa fa-trash red"></i>
                          </a>
                      </td>
                    </tr>
                  </tbody>
                </table>
              </div>
              <!-- /.card-body -->
			  <div class="card-footer">
				  <pagination :data="users" @pagination-change-page="getResults"></pagination>
			  </div>
            </div>
            <!-- /.card -->
          </div>
        </div>

		<div v-if="!$gate.isAdminOrAuthor()">
			<nod-found></nod-found>
		</div>
        <!-- Modal -->
<div class="modal fade" id="addNew" tabindex="-1" aria-labelledby="addNewLabel" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" v-show="!editmode" id="addNewLabel">Добавить нового пользователя</h5>
		<h5 class="modal-title" v-show="editmode" id="addNewLabel">Редактирование пользователя</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
	  <form @submit.prevent="editmode ? updateUser() : createUser()">
		<div class="modal-body">
			<div class="form-group">
				<input v-model="form.name" type="text" name="name" 
					placeholder="Введите имя"
					class="form-control" :class="{ 'is-invalid': form.errors.has('name') }">
				<has-error :form="form" field="name"></has-error>
			</div>
			<div class="form-group">
				<input v-model="form.email" type="email" name="email" 
					placeholder="Введите email"
					class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
				<has-error :form="form" field="email"></has-error>
			</div>
			<div class="form-group">
				<textarea v-model="form.bio" type="bio" name="bio" id="bio" 
					placeholder="Краткая биография для пользователей"
					class="form-control" :class="{ 'is-invalid': form.errors.has('email') }">
				</textarea>
				<has-error :form="form" field="bio"></has-error>
			</div>
			<div class="form-group">
				<select name="type" v-model="form.type" id="type" 
					class="form-control" :class="{ 'is-invalid': form.errors.has('type') }">
					<option value="">Выберите Роль</option>
					<option value="admin">Admin</option>
					<option value="user">Пользователь</option>
					<option value="author">Автор</option>	
				</select>
				<has-error :form="form" field="type"></has-error>
			</div>
			<div class="form-group">
				<input v-model="form.password" type="password" name="password" id="password" 
					placeholder="Введите пароль"
					class="form-control" :class="{ 'is-invalid': form.errors.has('password') }">
				<has-error :form="form" field="password"></has-error>
			</div>
		</div>
		<div class="modal-footer">
			<button type="button" class="btn btn-danger" data-dismiss="modal">Закрыть</button>
			<button v-show="editmode" type="submit" class="btn btn-success">Обновить</button>
			<button v-show="!editmode" type="submit" class="btn btn-primary">Сохранить</button>
		</div>
	</form>
	</div>
  </div>
</div>
    </div>
</template>

<script>
    export default {
      data() {
        return {
			editmode: false,
			users: {},
            form: new Form({
				id: '',
				name: '',
				email: '',
				password: '',
				type: '',
				bio: '',
				photo: ''
          })
        }
      },
	  methods: {
		  // Our method to GET results from a Laravel endpoint
		getResults(page = 1) {
			axios.get('api/user?page=' + page)
				.then(response => {
					this.users = response.data;
				});
			},
		  updateUser() {
			  this.$Progress.start();
			  //console.log('Добавлено значение');
			  this.form.put('api/user/'+this.form.id)
			  .then(() => {
				  $('#addNew').modal('hide');
				  swal.fire(
					'Успешно!',
					'Пользователь обновлен',
					'success'
				)
				this.$Progress.finish();
				Fire.$emit('AfterCreate');
			})
			  .catch(() => {
				  this.$Progress.fail();
			  })
		  },
		  editModal(user) {
			this.editmode = true;
			this.form.reset();
			$('#addNew').modal('show');
			this.form.fill(user);
		  },
		  newModal() {
			  this.editmode = false;
			  this.form.reset();
			$('#addNew').modal('show');
		  },
		  deleteUser(id) {
			  swal.fire({
				title: 'Вы уверены?',
				text: "Вы можете отменить это!",
				icon: 'warning',
				showCancelButton: false,
				confirmButtonColor: '#3085d6',
				cancelButtonColor: '#d33',
				confirmButtonText: 'Да, удалить!'
				}).then((result) => {

					// Send request to the server
					if (result.value) {
						this.form.delete('api/user/' + id).then(() => {
							swal.fire(
							'Удалено!!',
							'Пользователь удален',
							'success'
							)
							Fire.$emit('AfterCreate');
						}).catch(() => {
							swal("Ошибка!");
						});
					}
				})
		  },
		  loadUsers() {
			  if (this.$gate.isAdminOrAuthor()) {
				  axios.get("api/user").then(({ data })=>(this.users = data));
			  }
		  },
		  createUser() {
			  this.$Progress.start();
			  this.form.post('api/user')
			  .then(()=>{
				  Fire.$emit('AfterCreate')
				  $('#addNew').modal('hide')
			  toast({
				type: 'success',
				title: "Пользователь создан!"
				})	 
			})
			.catch(()=>{
				this.$Progress.fail();
			})
		  }
	  },
        created() {
			Fire.$on('searching',() => {
				let query = this.$parent.search;
				axios.get('api/findUser?q=' + query)
				.then((data) => {
					this.users = data.data
				})
				.catch(() => {

				})
			});
			this.loadUsers();
			Fire.$on('AfterCreate',() => {
				this.loadUsers();
			});
			//setInterval(() => this.loadUsers(), 3000);
        }
    }
</script>
