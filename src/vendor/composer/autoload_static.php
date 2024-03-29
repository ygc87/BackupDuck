<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1bf98f3283e9bc59b0589ba699ec8277
{
    public static $files = array (
        '0e6d7bf4a5811bfa5cf40c5ccd6fae6a' => __DIR__ . '/..' . '/symfony/polyfill-mbstring/bootstrap.php',
        '3b5531f8bb4716e1b6014ad7e734f545' => __DIR__ . '/..' . '/illuminate/support/Illuminate/Support/helpers.php',
        '2caf03c45be07a6255d0534f11d85d1d' => __DIR__ . '/../../..' . '/src/old/OpenSociax/functions.inc.php',
    );

    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'Ts\\' => 3,
        ),
        'S' => 
        array (
            'Symfony\\Polyfill\\Mbstring\\' => 26,
            'Symfony\\Component\\Translation\\' => 30,
            'Symfony\\Component\\Filesystem\\' => 29,
        ),
        'M' => 
        array (
            'Medz\\Component\\Filesystem\\' => 26,
        ),
        'C' => 
        array (
            'Carbon\\' => 7,
        ),
        'A' => 
        array (
            'App\\' => 4,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Ts\\' => 
        array (
            0 => __DIR__ . '/../../..' . '/src',
        ),
        'Symfony\\Polyfill\\Mbstring\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/polyfill-mbstring',
        ),
        'Symfony\\Component\\Translation\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/translation',
        ),
        'Symfony\\Component\\Filesystem\\' => 
        array (
            0 => __DIR__ . '/..' . '/symfony/filesystem',
        ),
        'Medz\\Component\\Filesystem\\' => 
        array (
            0 => __DIR__ . '/..' . '/medz/filesystem/src',
        ),
        'Carbon\\' => 
        array (
            0 => __DIR__ . '/..' . '/nesbot/carbon/src/Carbon',
        ),
        'App\\' => 
        array (
            0 => __DIR__ . '/../../..' . '/apps',
        ),
    );

    public static $prefixesPsr0 = array (
        'I' => 
        array (
            'Illuminate\\Support' => 
            array (
                0 => __DIR__ . '/..' . '/illuminate/support',
            ),
            'Illuminate\\Events' => 
            array (
                0 => __DIR__ . '/..' . '/illuminate/events',
            ),
            'Illuminate\\Database' => 
            array (
                0 => __DIR__ . '/..' . '/illuminate/database',
            ),
            'Illuminate\\Container' => 
            array (
                0 => __DIR__ . '/..' . '/illuminate/container',
            ),
        ),
    );

    public static $classMap = array (
        'AbstractAddons' => __DIR__ . '/../../..' . '/src/old/OpenSociax/addons/AbstractAddons.class.php',
        'Action' => __DIR__ . '/../../..' . '/src/old/OpenSociax/Action.class.php',
        'AddonDataModel' => __DIR__ . '/../../..' . '/addons/model/AddonDataModel.class.php',
        'AddonModel' => __DIR__ . '/../../..' . '/addons/model/AddonModel.class.php',
        'Addons' => __DIR__ . '/../../..' . '/src/old/OpenSociax/Addons.class.php',
        'AddonsInterface' => __DIR__ . '/../../..' . '/src/old/OpenSociax/addons/AbstractAddons.class.php',
        'AlipayNotify' => __DIR__ . '/../../..' . '/addons/library/alipay/lib/alipay_notify.class.php',
        'AlipaySubmit' => __DIR__ . '/../../..' . '/addons/library/alipay/lib/alipay_submit.class.php',
        'AnnouncementModel' => __DIR__ . '/../../..' . '/addons/model/AnnouncementModel.class.php',
        'AnnouncementWidget' => __DIR__ . '/../../..' . '/addons/widget/AnnouncementWidget/AnnouncementWidget.class.php',
        'Api' => __DIR__ . '/../../..' . '/src/old/OpenSociax/Api.class.php',
        'App' => __DIR__ . '/../../..' . '/src/old/OpenSociax/App.class.php',
        'AppModel' => __DIR__ . '/../../..' . '/addons/model/AppModel.class.php',
        'AreaModel' => __DIR__ . '/../../..' . '/addons/model/AreaModel.class.php',
        'AreaWidget' => __DIR__ . '/../../..' . '/addons/widget/AreaWidget/AreaWidget.class.php',
        'AreasWidget' => __DIR__ . '/../../..' . '/addons/widget/AreasWidget/AreasWidget.class.php',
        'AtmeModel' => __DIR__ . '/../../..' . '/addons/model/AtmeModel.class.php',
        'AttachModel' => __DIR__ . '/../../..' . '/addons/model/AttachModel.class.php',
        'AvatarBoxWidget' => __DIR__ . '/../../..' . '/addons/widget/AvatarBoxWidget/AvatarBoxWidget.class.php',
        'AvatarModel' => __DIR__ . '/../../..' . '/addons/model/AvatarModel.class.php',
        'AvatarWidget' => __DIR__ . '/../../..' . '/addons/widget/AvatarWidget/AvatarWidget.class.php',
        'AvatarsWidget' => __DIR__ . '/../../..' . '/addons/widget/AvatarsWidget/AvatarsWidget.class.php',
        'BaseModel' => __DIR__ . '/../../..' . '/addons/model/BaseModel.class.php',
        'BlacklistWidget' => __DIR__ . '/../../..' . '/addons/widget/BlacklistWidget/BlacklistWidget.class.php',
        'BlogCategoryModel' => __DIR__ . '/../../..' . '/addons/model/BlogCategoryModel.class.php',
        'Cache' => __DIR__ . '/../../..' . '/src/old/OpenSociax/Cache.class.php',
        'CacheApc' => __DIR__ . '/../../..' . '/addons/library/cache/CacheApc.class.php',
        'CacheBae' => __DIR__ . '/../../..' . '/addons/library/cache/CacheBae.class.php',
        'CacheDb' => __DIR__ . '/../../..' . '/addons/library/cache/CacheDb.class.php',
        'CacheEaccelerator' => __DIR__ . '/../../..' . '/addons/library/cache/CacheEaccelerator.class.php',
        'CacheFile' => __DIR__ . '/../../..' . '/addons/library/cache/CacheFile.class.php',
        'CacheMemcache' => __DIR__ . '/../../..' . '/addons/library/cache/CacheMemcache.class.php',
        'CacheModel' => __DIR__ . '/../../..' . '/addons/model/CacheModel.class.php',
        'CacheRedis' => __DIR__ . '/../../..' . '/addons/library/cache/CacheRedis.class.php',
        'CacheWincache' => __DIR__ . '/../../..' . '/addons/library/cache/CacheWincache.class.php',
        'CacheXcache' => __DIR__ . '/../../..' . '/addons/library/cache/CacheXcache.class.php',
        'CanvasModel' => __DIR__ . '/../../..' . '/addons/model/CanvasModel.class.php',
        'CateTreeModel' => __DIR__ . '/../../..' . '/addons/model/CateTreeModel.class.php',
        'CategoryTreeModel' => __DIR__ . '/../../..' . '/addons/model/CategoryTreeModel.class.php',
        'CategoryWidget' => __DIR__ . '/../../..' . '/addons/widget/CategoryWidget/CategoryWidget.class.php',
        'CheckInWidget' => __DIR__ . '/../../..' . '/addons/widget/CheckInWidget/CheckInWidget.class.php',
        'CloudAttachModel' => __DIR__ . '/../../..' . '/addons/model/CloudAttachModel.class.php',
        'CloudImageModel' => __DIR__ . '/../../..' . '/addons/model/CloudImageModel.class.php',
        'CollectionModel' => __DIR__ . '/../../..' . '/addons/model/CollectionModel.class.php',
        'CollectionWidget' => __DIR__ . '/../../..' . '/addons/widget/CollectionWidget/CollectionWidget.class.php',
        'ColorWidget' => __DIR__ . '/../../..' . '/addons/widget/ColorWidget/ColorWidget.class.php',
        'CommentDiggModel' => __DIR__ . '/../../..' . '/addons/model/CommentDiggModel.class.php',
        'CommentModel' => __DIR__ . '/../../..' . '/addons/model/CommentModel.class.php',
        'CommentVideoModel' => __DIR__ . '/../../..' . '/addons/model/CommentVideoModel.class.php', 
        'CommentWidget' => __DIR__ . '/../../..' . '/addons/widget/CommentWidget/CommentWidget.class.php',
        'Config\\Store' => __DIR__ . '/../../..' . '/addons/library/GatewayClient/Config/Store.php',
        'Context' => __DIR__ . '/../../..' . '/addons/library/GatewayClient/Gateway.php',
        'CreditModel' => __DIR__ . '/../../..' . '/addons/model/CreditModel.class.php',
        'CryptDES' => __DIR__ . '/../../..' . '/addons/library/CryptDES.php',
        'DES_MOBILE' => __DIR__ . '/../../..' . '/addons/library/DES_MOBILE.php',
        'DateSelectWidget' => __DIR__ . '/../../..' . '/addons/widget/DateSelectWidget/DateSelectWidget.class.php',
        'Db' => __DIR__ . '/../../..' . '/src/old/OpenSociax/Db.class.php',
        'DenouceWidget' => __DIR__ . '/../../..' . '/addons/widget/DenouceWidget/DenouceWidget.class.php',
        'DenounceModel' => __DIR__ . '/../../..' . '/addons/model/DenounceModel.class.php',
        'DepartmentModel' => __DIR__ . '/../../..' . '/addons/model/DepartmentModel.class.php',
        'DepartmentWidget' => __DIR__ . '/../../..' . '/addons/widget/DepartmentWidget/DepartmentWidget.class.php',
        'DiggWidget' => __DIR__ . '/../../..' . '/addons/widget/DiggWidget/DiggWidget.class.php',
        'Dir' => __DIR__ . '/../../..' . '/addons/library/io/Dir.class.php',
        'DirectoryIterator' => __DIR__ . '/../../..' . '/addons/library/io/Dir.class.php',
        'DisableUserModel' => __DIR__ . '/../../..' . '/addons/model/DisableUserModel.class.php',
        'DiyCustom' => __DIR__ . '/../../..' . '/addons/diywidget/Tags/w/DiyCustom.php',
        'DiyImage' => __DIR__ . '/../../..' . '/addons/diywidget/Tags/w/DiyImage.php',
        'DiySendFrame' => __DIR__ . '/../../..' . '/addons/diywidget/Tags/w/DiySendFrame.php',
        'DiyTab' => __DIR__ . '/../../..' . '/addons/diywidget/Tags/w/DiyTab.php',
        'DiyUser' => __DIR__ . '/../../..' . '/addons/diywidget/Tags/w/DiyUser.php',
        'DiyVideo' => __DIR__ . '/../../..' . '/addons/diywidget/Tags/w/DiyVideo.php',
        'DiyWeibo' => __DIR__ . '/../../..' . '/addons/diywidget/Tags/w/DiyWeibo.php',
        'DiyWidget' => __DIR__ . '/../../..' . '/addons/widget/DiyWidget/DiyWidget.class.php',
        'DiyWidgetModel' => __DIR__ . '/../../..' . '/addons/model/DiyWidgetModel.class.php',
        'EditorWidget' => __DIR__ . '/../../..' . '/addons/widget/EditorWidget/EditorWidget.class.php',
        'EventModel' => __DIR__ . '/../../..' . '/addons/model/EventModel1.class.php',
        'ExpressionModel' => __DIR__ . '/../../..' . '/addons/model/ExpressionModel.class.php',
        'FeedDiggModel' => __DIR__ . '/../../..' . '/addons/model/FeedDiggModel.class.php',
        'FeedListWidget' => __DIR__ . '/../../..' . '/addons/widget/FeedListWidget/FeedListWidget.class.php',
        'FeedManageWidget' => __DIR__ . '/../../..' . '/addons/widget/FeedManageWidget/FeedManageWidget.class.php',
        'FeedModel' => __DIR__ . '/../../..' . '/addons/model/FeedModel.class.php',
        'FeedTopicAdminModel' => __DIR__ . '/../../..' . '/addons/model/FeedTopicAdminModel.class.php',
        'FeedTopicModel' => __DIR__ . '/../../..' . '/addons/model/FeedTopicModel.class.php',
        'FeedbackModel' => __DIR__ . '/../../..' . '/addons/model/FeedbackModel.class.php',
        'FileStore' => __DIR__ . '/../../..' . '/addons/library/GatewayClient/Gateway.php',
        'FollowBtnWidget' => __DIR__ . '/../../..' . '/addons/widget/FollowBtnWidget/FollowBtnWidget.class.php',
        'FollowGroupModel' => __DIR__ . '/../../..' . '/addons/model/FollowGroupModel.class.php',
        'FollowGroupWidget' => __DIR__ . '/../../..' . '/addons/widget/FollowGroupWidget/FollowGroupWidget.class.php',
        'FollowModel' => __DIR__ . '/../../..' . '/addons/model/FollowModel.class.php',
        'FormInputWidget' => __DIR__ . '/../../..' . '/addons/widget/FormInputWidget/FormInputWidget.class.php',
        'Ftp' => __DIR__ . '/../../..' . '/addons/library/Ftp.class.php',
        'Gateway' => __DIR__ . '/../../..' . '/addons/library/GatewayClient/Gateway.php',
        'GatewayProtocol' => __DIR__ . '/../../..' . '/addons/library/GatewayClient/Gateway.php',
        'GdReflectionLib' => __DIR__ . '/../../..' . '/addons/library/phpthumb/thumb_plugins/gd_reflection.inc.php',
        'GdThumb' => __DIR__ . '/../../..' . '/addons/library/phpthumb/GdThumb.inc.php',
        'Hooks' => __DIR__ . '/../../..' . '/src/old/OpenSociax/addons/Hooks.class.php',
        'HotPostWidget' => __DIR__ . '/../../..' . '/addons/widget/HotPostWidget/HotPostWidget.class.php',
        'Http' => __DIR__ . '/../../..' . '/addons/library/Http.class.php',
        'Image' => __DIR__ . '/../../..' . '/addons/library/Image.class.php',
        'InviteFriendWidget' => __DIR__ . '/../../..' . '/addons/widget/InviteFriendWidget/InviteFriendWidget.class.php',
        'InviteModel' => __DIR__ . '/../../..' . '/addons/model/InviteModel.class.php',
        'LangModel' => __DIR__ . '/../../..' . '/addons/model/LangModel.class.php',
        'Log' => __DIR__ . '/../../..' . '/src/old/OpenSociax/Log.class.php',
        'LogsModel' => __DIR__ . '/../../..' . '/addons/model/LogsModel.class.php',
        'MailModel' => __DIR__ . '/../../..' . '/addons/model/MailModel.class.php',
        'ManageWidget' => __DIR__ . '/../../..' . '/addons/widget/ManageWidget/ManageWidget.class.php',
        'MedalListWidget' => __DIR__ . '/../../..' . '/addons/widget/MedalListWidget/MedalListWidget.class.php',
        'MedalModel' => __DIR__ . '/../../..' . '/addons/model/MedalModel.class.php',
        'MedzValidator' => __DIR__ . '/../../..' . '/addons/utility/MedzValidator.php',
        'MedzZip' => __DIR__ . '/../../..' . '/addons/utility/MedzZip.php',
        'Medz\\Component\\EmojiFormat' => __DIR__ . '/..' . '/medz/emoji-format/EmojiFormat.php',
        'MessageModel' => __DIR__ . '/../../..' . '/addons/model/MessageModel.class.php',
        'Model' => __DIR__ . '/../../..' . '/src/old/OpenSociax/Model.class.php',
        'Modelite' => __DIR__ . '/../../..' . '/src/old/OpenSociax/Modelite.class.php',
        'MySpaceCountWidget' => __DIR__ . '/../../..' . '/addons/widget/MySpaceCountWidget/MySpaceCountWidget.class.php',
        'NaviModel' => __DIR__ . '/../../..' . '/addons/model/NaviModel.class.php',
        'NewsModel' => __DIR__ . '/../../..' . '/addons/model/NewsModel.class.php',
        'NormalAddons' => __DIR__ . '/../../..' . '/src/old/OpenSociax/addons/NormalAddons.class.php',
        'NotifyModel' => __DIR__ . '/../../..' . '/addons/model/NotifyModel.class.php',
        'OldVideoModel' => __DIR__ . '/../../..' . '/addons/model/VideoModel.class.php',
        'OldWeibaDarenWidget' => __DIR__ . '/../../..' . '/addons/widget/WeibaDarenWidget/WeibaDarenWidget.class.php',
        'OnlineModel' => __DIR__ . '/../../..' . '/addons/model/OnlineModel.class.php',
        'PHPMailer' => __DIR__ . '/../../..' . '/addons/library/phpmailer/class.phpmailer.php',
        'POP3' => __DIR__ . '/../../..' . '/addons/library/phpmailer/class.pop3.php',
        'Page' => __DIR__ . '/../../..' . '/src/old/OpenSociax/Page.class.php',
        'PageModel' => __DIR__ . '/../../..' . '/addons/model/PageModel.class.php',
        'ParseTagModel' => __DIR__ . '/../../..' . '/addons/model/ParseTagModel.class.php',
        'PassportModel' => __DIR__ . '/../../..' . '/addons/model/PassportModel.class.php',
        'PclZip' => __DIR__ . '/../../..' . '/addons/library/pclzip-2-8-2/pclzip.lib.php',
        'PermissionModel' => __DIR__ . '/../../..' . '/addons/model/PermissionModel.class.php',
        'PhpThumb' => __DIR__ . '/../../..' . '/addons/library/phpthumb/PhpThumb.inc.php',
        'PhpThumbFactory' => __DIR__ . '/../../..' . '/addons/library/phpthumb/ThumbLib.inc.php',
        'PinYinModel' => __DIR__ . '/../../..' . '/addons/model/PinYinModel.class.php',
        'Pinyin' => __DIR__ . '/..' . '/medz/vendor-pack-pinyin/Pinyin.php',
        'PicObjectModel' => __DIR__ . '/../../..' . '/addons/model/PicObjectModel.class.php',
        'PlotWidget' => __DIR__ . '/../../..' . '/addons/widget/PlotWidget/PlotWidget.class.php',
        'Redisd' => __DIR__ . '/../../..' . '/addons/library/GatewayClient/Gateway.php',
        'RegisterModel' => __DIR__ . '/../../..' . '/addons/model/RegisterModel.class.php',
        'RelatedDarenWidget' => __DIR__ . '/../../..' . '/addons/widget/RelatedDarenWidget/RelatedDarenWidget.class.php',
        'RelatedGroupWidget' => __DIR__ . '/../../..' . '/addons/widget/RelatedGroupWidget/RelatedGroupWidget.class.php',
        'RelatedUserModel' => __DIR__ . '/../../..' . '/addons/model/RelatedUserModel.class.php',
        'RelatedUserWidget' => __DIR__ . '/../../..' . '/addons/widget/RelatedUserWidget/RelatedUserWidget.class.php',
        'RelatedZhangguiWidget' => __DIR__ . '/../../..' . '/addons/widget/RelatedZhangguiWidget/RelatedZhangguiWidget.class.php',
        'RemarkWidget' => __DIR__ . '/../../..' . '/addons/widget/RemarkWidget/RemarkWidget.class.php',
        'SMTP' => __DIR__ . '/../../..' . '/addons/library/phpmailer/class.smtp.php',
        'SameUserWidget' => __DIR__ . '/../../..' . '/addons/widget/SameUserWidget/SameUserWidget.class.php',
        'ScheduleModel' => __DIR__ . '/../../..' . '/addons/model/ScheduleModel.class.php',
        'SearchModel' => __DIR__ . '/../../..' . '/addons/model/SearchModel.class.php',
        'SearchUserWidget' => __DIR__ . '/../../..' . '/addons/widget/SearchUserWidget/SearchUserWidget.class.php',
        'SelectFriendsWidget' => __DIR__ . '/../../..' . '/addons/widget/SelectFriendsWidget/SelectFriendsWidget.class.php',
        'SendWeiboWidget' => __DIR__ . '/../../..' . '/addons/widget/SendWeiboWidget/SendWeiboWidget.class.php',
        'SensitiveWordModel' => __DIR__ . '/../../..' . '/addons/model/SensitiveWordModel.class.php',
        'SessionDb' => __DIR__ . '/../../..' . '/src/old/OpenSociax/Session/SessionDb.class.php',
        'ShareModel' => __DIR__ . '/../../..' . '/addons/model/ShareModel.class.php',
        'ShareToFeedWidget' => __DIR__ . '/../../..' . '/addons/widget/ShareToFeedWidget/ShareToFeedWidget.class.php',
        'ShareWidget' => __DIR__ . '/../../..' . '/addons/widget/ShareWidget/ShareWidget.class.php',
        'ShowAdsWidget' => __DIR__ . '/../../..' . '/addons/widget/ShowAdsWidget/ShowAdsWidget.class.php',
        'ShowImgWidget' => __DIR__ . '/../../..' . '/addons/widget/ShowImgWidget/ShowImgWidget.class.php',
        'SimpleAddons' => __DIR__ . '/../../..' . '/src/old/OpenSociax/addons/SimpleAddons.class.php',
        'SmsModel' => __DIR__ . '/../../..' . '/addons/model/SmsModel.class.php',
        'SourceModel' => __DIR__ . '/../../..' . '/addons/model/SourceModel.class.php',
        'SphinxModel' => __DIR__ . '/../../..' . '/addons/model/SphinxModel.class.php',
        'Store' => __DIR__ . '/../../..' . '/addons/library/GatewayClient/Gateway.php',
        'String' => __DIR__ . '/../../..' . '/addons/library/String.class.php',
        'StringTextWidget' => __DIR__ . '/../../..' . '/addons/widget/StringTextWidget/StringTextWidget.class.php',
        'TagLib' => __DIR__ . '/../../..' . '/src/old/OpenSociax/TagLib.class.php',
        'TagLibCx' => __DIR__ . '/../../..' . '/src/old/OpenSociax/TagLib/TagLibCx.class.php',
        'TagLibHtml' => __DIR__ . '/../../..' . '/src/old/OpenSociax/TagLib/TagLibHtml.class.php',
        'TagModel' => __DIR__ . '/../../..' . '/addons/model/TagModel.class.php',
        'TagWidget' => __DIR__ . '/../../..' . '/addons/widget/TagWidget/TagWidget.class.php',
        'TagsAbstract' => __DIR__ . '/../../..' . '/src/old/OpenSociax/addons/TagsAbstract.class.php',
        'TaskCustomModel' => __DIR__ . '/../../..' . '/addons/model/TaskCustomModel.class.php',
        'TaskModel' => __DIR__ . '/../../..' . '/addons/model/TaskModel.class.php',
        'Template' => __DIR__ . '/../../..' . '/src/old/OpenSociax/Template.class.php',
        'TemplateModel' => __DIR__ . '/../../..' . '/addons/model/TemplateModel.class.php',
        'Think' => __DIR__ . '/../../..' . '/src/old/OpenSociax/Think.class.php',
        'ThinkException' => __DIR__ . '/../../..' . '/src/old/OpenSociax/ThinkException.class.php',
        'ThirdpartyShareWidget' => __DIR__ . '/../../..' . '/addons/widget/ThirdpartyShareWidget/ThirdpartyShareWidget.class.php',
        'ThumbBase' => __DIR__ . '/../../..' . '/addons/library/phpthumb/ThumbBase.inc.php',
        'TipsModel' => __DIR__ . '/../../..' . '/addons/model/TipsModel.class.php',
        'TipsWidget' => __DIR__ . '/../../..' . '/addons/widget/TipsWidget/TipsWidget.class.php',
        'TopicListWidget' => __DIR__ . '/../../..' . '/addons/widget/TopicListWidget/TopicListWidget.class.php',
        'TopicUserWidget' => __DIR__ . '/../../..' . '/addons/widget/TopicUserWidget/TopicUserWidget.class.php',
        'Ts' => __DIR__ . '/../../..' . '/src/Ts.php',
        'UMWidget' => __DIR__ . '/../../..' . '/addons/widget/UMWidget/UMWidget.class.php',
        'UnionBtnWidget' => __DIR__ . '/../../..' . '/addons/widget/UnionBtnWidget/UnionBtnWidget.class.php',
        'UnionModel' => __DIR__ . '/../../..' . '/addons/model/UnionModel.class.php',
        'UpYun' => __DIR__ . '/../../..' . '/addons/library/upyun.class.php',
        'UploadAttachWidget' => __DIR__ . '/../../..' . '/addons/widget/UploadAttachWidget/UploadAttachWidget.class.php',
        'UploadFile' => __DIR__ . '/../../..' . '/addons/library/UploadFile.class.php',
        'UploadWidget' => __DIR__ . '/../../..' . '/addons/widget/UploadWidget/UploadWidget.class.php',
        'UserAppModel' => __DIR__ . '/../../..' . '/addons/model/UserAppModel.class.php',
        'UserBlacklistModel' => __DIR__ . '/../../..' . '/addons/model/UserBlacklistModel.class.php',
        'UserCategoryModel' => __DIR__ . '/../../..' . '/addons/model/UserCategoryModel.class.php',
        'UserCategoryWidget' => __DIR__ . '/../../..' . '/addons/widget/UserCategoryWidget/UserCategoryWidget.class.php',
        'UserCategorysWidget' => __DIR__ . '/../../..' . '/addons/widget/UserCategorysWidget/UserCategorysWidget.class.php',
        'UserCountModel' => __DIR__ . '/../../..' . '/addons/model/UserCountModel.class.php',
        'UserCountWidget' => __DIR__ . '/../../..' . '/addons/widget/UserCountWidget/UserCountWidget.class.php',
        'UserDataModel' => __DIR__ . '/../../..' . '/addons/model/UserDataModel.class.php',
        'UserGroupLinkModel' => __DIR__ . '/../../..' . '/addons/model/UserGroupLinkModel.class.php',
        'UserGroupModel' => __DIR__ . '/../../..' . '/addons/model/UserGroupModel.class.php',
        'UserInformationWidget' => __DIR__ . '/../../..' . '/addons/widget/UserInformationWidget/UserInformationWidget.class.php',
        'UserModel' => __DIR__ . '/../../..' . '/addons/model/UserModel.class.php',
        'UserOfficialCategoryModel' => __DIR__ . '/../../..' . '/addons/model/UserOfficialCategoryModel.class.php',
        'UserOfficialModel' => __DIR__ . '/../../..' . '/addons/model/UserOfficialModel.class.php',
        'UserPrivacyModel' => __DIR__ . '/../../..' . '/addons/model/UserPrivacyModel.class.php',
        'UserProfileModel' => __DIR__ . '/../../..' . '/addons/model/UserProfileModel.class.php',
        'UserVerifiedModel' => __DIR__ . '/../../..' . '/addons/model/UserVerifiedModel.class.php',
        'ValidationModel' => __DIR__ . '/../../..' . '/addons/model/ValidationModel.class.php',
        'VideoModel' => __DIR__ . '/../../..' . '/addons/model/VideoModel.class.php',
        'VideoDiggModel' => __DIR__ . '/../../..' . '/addons/model/VideoDiggModel.class.php',
        'VideoWidget' => __DIR__ . '/../../..' . '/addons/widget/VideoWidget/VideoWidget.class.php',
        'VisitorLoginWidget' => __DIR__ . '/../../..' . '/addons/widget/VisitorLoginWidget/VisitorLoginWidget.class.php',
        'WebMessageModel' => __DIR__ . '/../../..' . '/addons/model/WebMessageModel.class.php',
        'WeibaDarenWidget' => __DIR__ . '/../../..' . '/addons/widget/WeibaDarenWidget/WeibaDarenWidget.class.php',
        'WeibaReplyWidget' => __DIR__ . '/../../..' . '/addons/widget/WeibaReplyWidget/WeibaReplyWidget.class.php',
        'WeiboWidget' => __DIR__ . '/../../..' . '/addons/widget/WeiboWidget/WeiboWidget.class.php',
        'Widget' => __DIR__ . '/../../..' . '/src/old/OpenSociax/Widget.class.php',
        'WidgetModel' => __DIR__ . '/../../..' . '/addons/model/WidgetModel.class.php',
        'XarticleModel' => __DIR__ . '/../../..' . '/addons/model/XarticleModel.class.php',
        'XattachModel' => __DIR__ . '/../../..' . '/addons/model/XattachModel.class.php',
        'XconfigModel' => __DIR__ . '/../../..' . '/addons/model/XconfigModel.class.php',
        'XdataModel' => __DIR__ . '/../../..' . '/addons/model/XdataModel.class.php',
        'phpmailerException' => __DIR__ . '/../../..' . '/addons/library/phpmailer/class.phpmailer.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1bf98f3283e9bc59b0589ba699ec8277::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1bf98f3283e9bc59b0589ba699ec8277::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInit1bf98f3283e9bc59b0589ba699ec8277::$prefixesPsr0;
            $loader->classMap = ComposerStaticInit1bf98f3283e9bc59b0589ba699ec8277::$classMap;

        }, null, ClassLoader::class);
    }
}
