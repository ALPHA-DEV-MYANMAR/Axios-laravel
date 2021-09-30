@extends('layouts.master')

@section('title','home')

@section('content')

    <div class="container ">
        <div class="row">

            <div class="col-md-8">
                <legend>Posts</legend>
                <table class="table" >
                    <thead>
                      <tr>
                        <th scope="col">#</th>
                        <th scope="col">Name</th>
                        <th scope="col">Description</th>
                        <th scope="col">Action</th>
                      </tr>
                    </thead>
                    <tbody id="tablebody">

                    </tbody>
                  </table>
            </div>

            <div class="col-md-4">
                <div class="row">
                  <span id="successMsg"></span>
                  <legend>Create Posts</legend>
                    <form name="myForm">
                        <div class="form-group">
                            <label for="">Title</label>
                            <input type="text" name="title" class="form-control">
                        </div>
                        <div class="form-group">
                            <label for="">Description</label>
                            <textarea class="form-control" name="description" id="" rows="4"></textarea>
                        </div>
                        <button type="submit" class="btn btn-primary btn-block">Submit</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal -->
    <div class="modal fade" id="editModel" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel" aria-hidden="true">
      <div class="modal-dialog" role="document">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="exampleModalLabel">Modal title</h5>
            <button type="button" class="close" data-dismiss="modal" aria-label="Close">
              <span aria-hidden="true">&times;</span>
            </button>
          </div>
          <div class="modal-body">

            <form name="editForm" id="editModel">
              <span id="UpdateMsg"></span>
              <div class="form-group">
                  <label for="">Title</label>
                  <input type="text" name="title" class="form-control">
              </div>
              <div class="form-group">
                  <label for="">Description</label>
                  <textarea class="form-control" name="description" id="" rows="4"></textarea>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
                <button type="submit" class="btn btn-primary">Update</button>
              </div>

            </form>
          
          </div>

        </div>
      </div>
    </div>

    {{-- Axios cdn --}}
    <script src="https://cdn.jsdelivr.net/npm/axios/dist/axios.min.js"></script>

    <script>


        var tablebody = document.getElementById('tablebody');
        var titleList = document.getElementsByClassName('titleList');
        var descList = document.getElementsByClassName('descList');
        var idList = document.getElementsByClassName('idList');
        var btnList = document.getElementsByClassName('btnList');

        console.log(titleList,descList,idList,btnList);

        //Read
        axios.get('http://localhost/pj/axios/public/api/posts')
            .then(Response => {
              
              
              Response.data.forEach(item => {

               // console.log(item);

               ShowData(item);

              });

              //console.log(Response);

            })
            .catch(error => console.log(error));

            //Create
            var myForm = document.forms['myForm'];
            var titleinput = myForm['title'];
            var descriptioninput = myForm['description'];

            myForm.onsubmit = (function(e){
              e.preventDefault();

              axios.post('http://localhost/pj/axios/public/api/posts',{
                
                title: titleinput.value,
                description: descriptioninput.value,

              })
              .then(res => {
                console.log(res);
                document.getElementById('successMsg').innerHTML = '<div class="alert alert-success" role="alert"><strong>'+res.data.msg+
                '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                  
                ShowData(res.data[0]);

              })
              .catch(err => {
                console.error(err); 
              })

            });

            //Edit & Update
            var editForm = document.forms['editForm'];
            var editTitle = editForm['title'];
            var editDescription = editForm['description'];
            var UpdateId;
            var oldTitle;
            var oldDesc;

            //Edit
            function editData(postId){

                UpdateId = postId;

                axios.get('http://localhost/pj/axios/public/api/posts/'+postId)
                .then(res => {

                  console.log(res.data.description)

                  editTitle.value = res.data.title;
                  editDescription.value = res.data.description;

                  oldTitle = res.data.title;
                  oldDesc = res.data.description;

                  console.log(oldDesc);

                })
                .catch(err => {
                  //console.error(err); 
                })
            }

            //Update
            editForm.onsubmit = function(e){
              e.preventDefault();

              axios.put('http://localhost/pj/axios/public/api/posts/'+UpdateId,{

                title: editTitle.value,
                description: editDescription.value,
              
              })
              .then(res => {
                console.log(res.data.msg)
                document.getElementById('UpdateMsg').innerHTML = '<div class="alert alert-success" role="alert"><strong>'+res.data.msg+
                '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
                $('#editModel').modal('hide');

                  for(var i=0; i < titleList.length ;i++){
                      if(titleList[i].innerHTML == oldTitle){
                          titleList[i].innerHTML = editTitle.value;
                          descList[i].innerHTML = editDescription.value;
                      }
                  }

              })
              .catch(err => {
                console.error(err); 
              })

            }

            //Delete Data
            function deleteData(postId){

              axios.delete('http://localhost/pj/axios/public/api/posts/'+postId)
              .then(res => {
                console.log(res.data.deletetData);
                document.getElementById('successMsg').innerHTML = '<div class="alert alert-danger" role="alert"><strong>'+res.data.msg+
                '</strong><button type="button" class="close" data-dismiss="alert" aria-label="Close"><span aria-hidden="true">&times;</span></button></div>';
              
                  for(var i=0; i < titleList.length; i++){
                    if(titleList[i].innerHTML == res.data.deletetData.title){
                        titleList[i].style.display = 'none';
                        idList[i].style.display = 'none';
                        btnList[i].style.display = 'none';
                        descList[i].style.display = 'none';
                    }
                  }

              })
              .catch(err => {
                console.error(err); 
              })
            }


            //Upload data don't neet to refresh

            function ShowData(data){

              tablebody.innerHTML += 
                '<tr>'+
                    '<td class="idList">'+data.id+'</td>'+
                    '<td class="titleList">'+data.title+'</td>'+
                    '<td class="descList">'+data.description+'</td>'+
                    '<td class="btnList">'+
                        ' <button class="btn btn-success btn-sm" data-toggle="modal" data-target="#editModel" onclick="editData('+data.id+')">edit</button>'+
                        ' <button class="btn btn-danger btn-sm" onclick="deleteData('+data.id+')">delete</button>'+
                    '</td>'+
                '</tr>';

            }


    </script>

@endsection 

