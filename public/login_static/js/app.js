/**
 * Created by zengwei on 2017/10/30
 */

var vm = new Vue({
    el: '#app',
    data: {
        loginname: "",
        passwd: "",
        progressStatus: '',
        step: 0,
        progress_value: 0,
        lock: false,
        fullscreenLoading: false,
        uploadActive: false,
        loadingActive: false,
        progressActive: false,
        tableData: [],
        total: 100,
        loading: false,
        num: 5,
        hostListActive: false,
        role: '',
        token: ''
    },
    created: function () {

    },
    methods: {
        handleSelectionChange(val) {
            this.multipleSelection = val;
        },
        // 上传进度
        uploadProgress(event, file, fileList) {
            this.progressActive = true
            this.progress_value = parseInt(event.percent)
        },
        // 上传成功之后
        uploadSuccess(response, file, fileList) {
            this.progressStatus = "success"
            this.stopUpload()
            swal({
                type: 'success',
                text: 'license文件上传成功，请重新登陆！',
                background: '#282828',
                confirmButtonColor: '#666',
                textColor: '#fff'
            }).then(() => {
                this.uploadActive = false
                this.lock = false
            })
        },
        // 上传之前判断文件类型
        uploadBefore(file) {
            // console.log(file)
            if (file.name === 'license') {
                this.startUpload()
                return true
            }
            swal({
                type: 'error',
                text: '请上传license文件！',
                background: '#282828',
                confirmButtonColor: '#666',
                textColor: '#fff'
            })
            return false
        },
        // 开始上传
        startUpload() {
            this.loadingActive = true
        },
        // 停止上传
        stopUpload() {
            this.loadingActive = false
        },
        // 关闭按钮
        close() {
            this.lock = false
            this.uploadActive = false
        },
        closeHostList() {
            this.hostListActive = false
        },
        showModal: function (msg, fn, json = {}) {
            swal({
                type: json.type || 'error',
                text: msg,
                background: '#282828',
                confirmButtonColor: '#666',
                textColor: '#fff',
                showCancelButton: json.showCancelButton,
                cancelButtonText: json.cancelButtonText || '取消',
                confirmButtonText: json.confirmButtonText || '确定',
                allowOutsideClick: false,
                allowEscapeKey: false
            }).then(() => {
                if (fn) {
                    fn()
                }
                // 300毫秒之后开锁
                setTimeout(() => {
                    this.lock = false
                }, 300)
            })
        },
        // 显示上传快
        showUpload() {
            setTimeout(() => {
                this.loadingActive = false // 隐藏loading组件
                this.uploadActive = true // 显示上传组件块
                this.progressActive = false // 隐藏进度组件
                this.progressStatus = '' // 设置进度组件的颜色值为默认
                this.lock = true // 上传license文件的时候需要关闭input框回车事件
            }, 400)
            this.loadingActive = true
        },

        // 显示主机块
        showHostList() {
            this.getData('/login/findAllHost', '', ({data}, textStatus, request) => {
                console.log(data)
                data.data.forEach(o => {
                    o.web_version = o.os_type + '-' + o.os_version + '-' + o.os_arch
                })
                this.tableData = data.data
                this.num = data.num
                this.loadingActive = false // 隐藏loading组件
                this.hostListActive = true // 显示主机列表组件
                this.lock = true // 上传license文件的时候需要关闭input框回车事件
            })
            // setTimeout(() => {
            //     this.loadingActive = false // 隐藏loading组件
            //     this.hostListActive = true // 显示主机列表组件
            //     this.lock = true // 上传license文件的时候需要关闭input框回车事件
            // }, 400)
            this.loadingActive = true
        },

        pageChange(currentPage) {
            console.log(currentPage)
            setTimeout(() => {
                this.loadingActive = false // 隐藏loading组件
                this.lock = true // 上传license文件的时候需要关闭input框回车事件
            }, 400)
            this.loadingActive = true
        },

        delById(data) {
            let infoJson = {type: 'info', showCancelButton: true, confirmButtonText: '确定', cancelButtonText: '取消'}
            let successJson = {type: 'success'}
            this.showModal('确定要删除该主机吗?', () => {
                this.loadingActive = true
                this.getData('/login/deleteHost/' + data.host_ids, '', (msg) => {
                    this.loadingActive = false
                    this.showModal(`删除${data.name}主机成功!`, () => {
                        this.showHostList()
                    }, successJson)
                })
            }, infoJson)
        },

        getData(url, data, successFn, errorFn) {
            $.ajax({
                url: url,
                // type: 'get',
                type: 'post',
                contentType: 'application/json; charset=UTF-8',
                data: data,
                success: (data, textStatus, request) => {
                    successFn(data, textStatus, request)
                },
                error: (XMLHttpRequest, textStatus, errorThrown) => {
                    // 通常 textStatus 和 errorThrown 之中
                    // 只有一个会包含信息
                    //this; // 调用本次AJAX请求时传递的options参数
                    if (errorFn) {
                        errorFn()
                    }
                    this.loadingActive = false
                    this.showModal('网络错误！')
                },
                // dataType: 'jsonp',
                dataType: 'json'
            })
        },

        login: function () {
            // var URL = "/login/dologin"
            var URL = "index.php/login/dologin"
            // var URL = "http://192.168.0.115:9989/data.php?login_name=lee&login_pwd=123456"
            // var URL = "http://192.168.0.115:9989/data.php"
            //JSON.stringify（）
            var postData = JSON.stringify({
                'login_name': this.loginname,
                'login_pwd': this.passwd
            })
            // var postData = {
            //     'login_name': this.loginname,
            //     'login_pwd': this.passwd
            // }
            if (this.lock) return
            this.lock = true
            this.getData(URL, postData, (data, textStatus, request) => {
                if (data['status']) {
                    this.token = request.getResponseHeader("token");
                    switch (data.status) {
                        case 200:// 登陆成功
                            localStorage.setItem('token', request.getResponseHeader("token"))
                            // console.log(request.getResponseHeader("token"))
                            let postData = JSON.stringify({
                                'token': request.getResponseHeader("token"),
                            })
                            this.getData('index.php/login/loged', postData, res => {
                                this.role = res.data.role
                                // this.token = request.getResponseHeader("token")
                                this.token = res.data.token
                                let loged = this.$refs.loged
                                setTimeout(o => {
                                    loged.submit()
                                }, 0)
                            })
                            // window.location = '/login/loged?token=' + request.getResponseHeader("token");
                            break;
                        case 400:// lisence文件为空
                            this.showModal(data['msg'], this.showUpload)
                            break
                        case 630:// 非系统管理员登录
                            this.showModal('license文件过期或点数已超，请联系系统管理员处理！')
                            break
                        case 700:// license文件过期
                            this.showModal('license文件过期，请重新上传license文件。', this.showUpload)
                            break
                        case 701:// 点数太多
                            this.showModal('检测到主机点数不符合要求，请先删除主机。', this.showHostList)
                            break
                        case 702: // 主机校验不符
                            this.showModal('主机校验不符，请重新上传license文件。', this.showUpload)
                            break
                        default: // 各种错误
                            this.showModal(data['msg'])
                            break
                    }
                }
            })
        }
    }
})