<?php
namespace SableSoft\Phone\Helper;

// app use:
use Magento\Eav\Model\Entity\Attribute;
use Magento\Customer\Model\Customer;
// module use:
use SableSoft\Phone\Model\Config;
use SableSoft\Phone\Model\Config\Source\AuthMode;
use SableSoft\Phone\Model\Config\Source\RegMode;
use SableSoft\Core\Helper\Data as CoreHelper;

/**
 * Class Data
 *
 * @package SableSoft\Phone\Helper
 */
class Data extends CoreHelper {

    protected $module = 'phone';

    /**
     * Retrieve the email field config
     *
     * @return array
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getUsernameFieldConfig() :array {
        $attribute = $this->getPhoneAttribute();
        /** @var Config $config */
        $config = $this->getConfig();
        switch( $config->getValue('auth' ) ) {
            case AuthMode::MODE_CODE:
            case AuthMode::MODE_PHONE:
                $label = __( $attribute->getData('frontend_label') );
                $type = 'tel';
                $dataValidate = "{required:true}";
                break;
            case AuthMode::MODE_BOTH:
                $label = __( 'Email' ) . ' / ' .
                    __( $attribute->getData('frontend_label' ) );
                $type = 'text';
                $dataValidate = "{required:true}";
                break;
            default:
                $label = __('Email');
                $type = 'email';
                $dataValidate = "{required:true, 'validate-email':true}";
                break;
        }

        return [
            'label'         => $label,
            'type'          => $type,
            'data_validate' => $dataValidate
        ];
    }

    public function getLoginNote() : string {
        $note = __('If you have an account, sign in with your email address.');
        switch( $this->getConfigValue('auth') ) {
            case AuthMode::MODE_PHONE:
                $note = __('If you have an account, sign in with your phone number.'); break;
            case AuthMode::MODE_BOTH:
                $note = __('If you have an account, sign in with your phone number or email address.'); break;
            case AuthMode::MODE_CODE:
                $note = __('If you have an account, sign in by phone verify code.'); break;
            default: break;
        }

        return $note;
    }

    /**
     * @return bool
     */
    public function isCodeAuthMode() : bool {
        return AuthMode::MODE_CODE == $this->getConfigValue(Config::FIELD_AUTH_MODE );
    }

    /**
     * @return bool
     */
    public function isCodeRegMode() : bool {
        return RegMode::MODE_CODE == $this->getConfigValue(Config::FIELD_REG_MODE );
    }

    /**
     * @return string
     */
    public function getCodeSendUrl() : string {
        /** @var Config $config */
        $config = $this->getConfig();

        return $this->_urlBuilder->getUrl( $config::ROUTE_PHONE_CODE_SEND );
    }

    /**
     * @return Attribute
     * @throws \Magento\Framework\Exception\LocalizedException
     */
    public function getPhoneAttribute() : Attribute {
        return $this->getAttribute( Customer::ENTITY, Config::ATTRIBUTE_PHONE );
    }

    /**
     * @return string
     */
    public function getPhonePlaceholder() : string {
        $code = $this->getConfigValue( 'country', 'smsp' );
        return "+$code (__) ___-__-__";
    }
}
