<?php
/**
 * coding: utf-8 *
 * system OS: windows10 *
 * work Tools:Phpstorm *
 * language Ver: php7.3 *
 * agreement: PSR-1 to PSR-11 *
 * filename: IoC.Origin.Application.Controller *
 * version: 1.0 *
 * structure: common framework *
 * designer: 沈启威 *
 * developer: 沈启威 *
 * partner: 沈启威 *
 * chinese Context: Origin 行为模板元素内容描述封装
 */
namespace Origin;
# 封装结构
class Model
{
    # 类封装常量结构（Model）
    const MODEL_METHOD_MARK = "method"; # 请求类型
    const MODEL_MAPPING_MARK = "model"; # 请求模板对象
    const MODEL_MAPPING_COLUMN_NAME = "name"; # 请求元素名称
    const MODEL_MAPPING_COLUMN_TYPE = "type"; # 请求元素类型
    const MODEL_MAPPING_COLUMN_IS_TO = "is_to"; # 是否强制转化
    const MODEL_MAPPING_COLUMN_TO_VALID = "to_valid"; # 强制验证
    const MODEL_VALID_MARK = "valid"; # 请求强制验证模板
    const MODEL_VALID_COLUMN_MAX = "max"; # 模板值约束最大值范围
    const MODEL_VALID_COLUMN_MIN = "min"; # 模板值约束最小值范围
    const MODEL_VALID_NOT_NULL = "not_null"; # 模板约束不为空
    const MODEL_VALID_COLUMN_FORMAT_STRING = "format_string"; # 模板值约束正则表达式
    # 事务执行常量
    const ACTION_DATA_SOURCE_MARK = "data_source"; # 数据源指向
    const ACTION_TYPE_MARK = "action_type"; # 行为类型 select，insert，delete，update 操作行为,query状态下直接使用优先级规则
    const ACTION_MODEL_OBJECT_MARK = "model_object"; # 对应执行模板，模板既可以是数据库结构模板，也可以是query执行模板
    const ACTION_VIEWS_STATUS_MARK = "views_status"; # 对应显示界面调用状态，默认false（不进行调用），true （调用）
    const ACTION_VIEWS_OBJECT_MARK = "views_object"; # 对应显示模板名称
    const ACTION_SUCCESS_MARK = "action_success"; # 执行成功指向
    const ACTION_FAILED_MARK = "action_failed"; # 执行失败指向，常规操作流程中，该项可以直接用error代替
    const ACTION_ERROR_MARK = "action_error"; # 执行异常指向
    # mapping映射模板结构
    const MAPPING_TABLE_MARK = "table_name"; # 对象表名称
    const MAPPING_CYCLE_MARK = "cycle_time"; # 对象周期时间
    const MAPPING_MAJOR_MARK = "major_key"; # 主键标签
    const MAPPING_COLUMN_MARK = "column_list"; # 表元素列表
    const MAPPING_AUTO_INCREMENT_OPTION = "auto_increment"; # 自增键属性
    const MAPPING_COLUMN_OPTION = "column"; # 数据元素对象
    const MAPPING_FIELD_OPTION = "field"; # 数据字段对象
    const MAPPING_TYPE_OPTION = "type"; # 数据类型
    const MAPPING_SIZE_OPTION = "size"; # 数据最大值范围
    const MAPPING_IS_NULL_OPTION = "not_null"; # 空属性
    const MAPPING_DEFAULT_OPTION = "default"; # 默认值
    const MAPPING_QUERY_TYPE_OPTION = "query"; # 强制执行类型,select,insert,update,delete,all
    const MAPPING_PAGE_MARK = "page"; # 翻页标记
    const MAPPING_PAGE_SIZE = "page_size"; # 单页长度
    const MAPPING_PAGE_URI = 'page_uri'; # 跳转地址
    const MAPPING_PAGE_STYLE = 'page_style'; # 翻页样式
    const MAPPING_PAGE_NUMBER = 'page_number'; # 页码长度
    const MAPPING_PAGE_CURRENT = "page_current"; # 当前页识别标记
    # action执行模板结构
    const ACTION_QUERY_MARK = "query"; # 语句
    const ACTION_COLUMN_MARK = "column"; # 元素列表
    const ACTION_MAPPING_MARK = "mapping"; # 数据映射对象,映射对象是在对数据模板进行主动行为约束时，进行模板指向
    const ACTION_FIELD_MARK = "field"; # 字段列表
    const ACTION_WHERE_MARK = "where"; # 条件列表
    const ACTION_ORDER_MARK = "order"; # 排序列表
    const ACTION_GROUP_MARK = "group"; # 分组列表
    const ACTION_LIMIT_MARK = "limit"; # 范围列表
    const ACTION_LIMIT_BEGIN_MARK = "begin";
    const ACTION_LIMIT_LENGTH_MARK = "length";
    const ACTION_PAGE_MARK = "page"; # 翻页标记
    const ACTION_PAGE_QUERY = "count_query"; # count语句
    const ACTION_PAGE_SIZE = "page_size"; # 单页长度
    const ACTION_PAGE_URI = "page_uri"; # 跳转地址
    const ACTION_PAGE_STYLE = "page_style"; # 翻页样式
    const ACTION_PAGE_NUMBER = "page_number"; # 页码长度
    const ACTION_PAGE_CURRENT = "page_current"; # 当前页识别标记
}