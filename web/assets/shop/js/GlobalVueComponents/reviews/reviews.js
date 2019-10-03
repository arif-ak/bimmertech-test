Vue.component('section-reviews', {
  props: ['api', 'id', 'header', 'tokenname', 'buttonname', 'placeolder', 'loadmorelabel'],
  data: function () {
    return {
      newComment: '',
      comments: [],
      commentsSet: new Set(),
      loadPagination: 0,
      loadMoreShow: false,
      userLikesList: false,
      isAuth: false,
      token: null,
      loading: false,
      user: false,
      id: null,
      test: 0,
      selectedStars: 0
    }
  },

  mounted: function () {
    this.token = window.localStorage.getItem(this.tokenname)
    var that = this;
    EventBus.$on('isAuth', function (isAuth) {
      that.isAuth = isAuth;
      that.loadPagination = 0;
      that.comments = [];
      that.commentsSet.clear();
      that.loadComments(that.loadPagination);
      that.userLikesList = false;
      that.loadLikes(false);
    });
    EventBus.$on('user', function (user) {
      that.user = user;
    });
  },
  updated: function () {
    // console.log('updated: ');
    let _this = this;
    this.$nextTick(function () {
      // console.log('updated: $nextTick');
    });
  },
  methods: {
    commentDate: function (d) {
      var month_names = ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'];
      var date = new Date(d * 1000)
      var month = date.getMonth();
      var day = date.getDate();
      var year = date.getFullYear();
      return month_names[month] + ' ' + day + ', ' + year;
    },
    openLogin: function () {
      $('.user-login').popup('show');
    },
    userLikes: function (id) {
      var audit = false;
      for (let i = 0; i < this.userLikesList.length; i++) {
        if (this.userLikesList[i] == id) {
          audit = true;
        }
      }
      return audit;
    },
    parseUserName: function (userObj) {
      let name;
      if (userObj.name) {
        name = userObj.name.concat(' ', userObj.last_name);
      } else {
        name = this.cutEmail(userObj.email);
      }
      return name;
    },
    loadComments: function (page) {
      var that = this;
      this.$http.post('/api2/' + this.api.loadComments, {
          page: page,
          object_id: this.id
        })
        .then(response => {
          let res = response.data.map(key => {
            that.commentsSet.add({
              id: key.id,
              text: key.comment,
              image: key.image,
              date: that.commentDate(key.date),
              likeCount: key.likeCount,
              rating: key.rating,
              title: key.title,
              name: that.parseUserName(key.user)
            });
          });
          if (that.loadPagination == 0) {
            if (response.data.length == 3) {
              that.loadMoreShow = true;
            } else {
              that.loadMoreShow = false;
            }
          } else {
            if (response.data.length == 5) {
              that.loadMoreShow = true;
            } else {
              that.loadMoreShow = false;
            }
          }
          that.loadPagination++;
          that.comments = Array.from(that.commentsSet);
          return res;
        }).catch(error => {

        });


    },
    loadLikes: function (c, like) {
      postRequest(this.api.loadLikes, {
          client_token: this.token,
          object_id: this.id
        })
        .then(response => {
          this.userLikesList = response.data;
          if (c !== false) {
            if (like) {
              this.comments[c].likeCount++;
            } else {
              this.comments[c].likeCount--;
            }
          }
        }).catch(error => {
          this.userLikesList = [];
        });
    },
    like: function (id, like, c) {
      postRequest(this.api.like, {
          review_id: id,
          client_token: this.token,
          is_liked: like
        })
        .then(response => {
          if (response.data.token != undefined) {
            window.localStorage.setItem(this.tokenname, response.data.token);
            this.token = window.localStorage.getItem(this.tokenname);
          }
          this.loadLikes(c, like);
        }).catch(error => {

        });
    },
    sendComment: function () {
      var space = /([^\s])/;
      if (this.selectedStars >= 1 && space.test(this.newComment) != false) {
        this.loading = true;
        postRequest(this.api.sendComment + this.id, {
            rating: this.selectedStars,
            title: '1',
            comment: this.newComment
          })
          .then(response => {
            console.log(response);
            this.loading = false;
            this.newComment = '';
            this.selectedStars = 0;
            $('.bt.modal.thankForSubmission').modal('show');
          }).catch(error => {
            this.loading = false;
          });
      }
    },
    cutEmail: function (email) {
      return email.split('@')[0];
    }
  }
})