<?php
  class ErrorCode {
//验证错误
    const E_ACCOUNT_PASSWORD = 'Error_10001'; //账户或密码错误
    const E_PASSWORD = 'Error_10002'; //密码验证错误
    const E_SELECT_USERINFO = 'Error_10003'; //查询不到用户信息
    const E_CREATE_TOKEN = 'Error_10004'; //创建用户令牌失败
    const E_LOGIN_TIMEOUT = 'Error_10005'; //登录超时
    const E_VALIDATION_FAILS = 'Error_10006'; //验证失败，请检查是否在其他地方登陆
    const E_ADD_TEL = 'Error_10007'; //添加电话号码失败
    const E_ADD_ADRESS = 'Error_10008'; //添加地址失败
    const E_REFRESH_TOKEN = 'Error_10009'; //更新令牌失败
    const E_UPLOAD_COVER = 'Error_10010'; //请上传封面
    const E_UPLOAD_PICTURE = 'Error_10011'; //图片上传失败
    const E_PICTURE_FORMAT = 'Error_10012'; //上传图片格式错误，请使用JPG或PNG图片
    const E_UPLOAD_DATA = 'Error_10013'; //上传数据失败;
    const E_ADD_POSITION = 'Error_10014'; //上传定位数据失败
    const E_PROFILE_PICTURE = 'Error_10015'; //更新头像失败
    const E_PICTURE_NULL = 'Error_10016'; //失败，服务器没有获取到图片
    const E_SAVE_PICTURE = 'Error_10016'; //保存图片失败
    const E_PARAMS_ERROR = 'Error_10017'; //参数错误
    
    
//通用错误
    const E_SELECT_ERROR = 'Error_20001'; //查询错误
    const E_DATA_ERROR = 'Error_20002'; //数据格式有误
    
//注册错误
    const E_COMPANY_NAME = 'Error_30001'; //企业名称格式错误，请正确输入名称
    const E_VALIDATE_STRING = 'Error_30002'; //输入内容包含非法字符
    const E_CONFIRM_PASSWORD = 'Error_30003'; //您两次输入的密码不一致，请检查后重试
    const E_SECURITY_CODE = 'Error_30004'; //您的验证码错误，请检查后再次输入，或重新获取验证码
    const E_REGISTER_FAILS = 'Error_30005'; //注册失败
    const E_DELETE_CODE = 'Error_30006'; //销毁验证码失败
    
//微信端错误
    
    const WX_TOKEN_NULL = 'Error_40001'; //请登录后访问服务器
    const WX_ARRAY_NULL = 'Error_40002'; //关注的企业列表为空
    const WX_TOKEN_ERROR = 'Error_40003'; //令牌验证失败
    const WX_LOGIN_TIMEOUT = 'Error_40004'; //登录时间超时
    const WX_UPDATE_ERROR = 'Error_40005'; //更新数据失败
    const WX_SELECT_ERROR = 'Error_40006'; //查询数据失败
    const WX_PAGE_NULL = 'Error_40007'; //页码为空
    const WX_ID_NULL = 'Error_40008'; //id为空
    const WX_PARAMETER_ERROR = 'Error_40009'; //参数错误
    const WX_LOGIN_FAILED = 'Error_40011'; //登录失败
    const WX_SELECT_ACCOUNT_FAILED = 'Error_40012'; //查询账户失败
    const WX_CREAT_TOKEN_ERROR = 'Error_40013'; //生成token失败
    const WX_UPDATE_TOKEN_FAILED = 'Error_40014'; //更新令牌失败
    const WX_SELECT_ATTENTION_FAILED = 'Error_40015'; //查询收藏失败
  }
  
  
?>
