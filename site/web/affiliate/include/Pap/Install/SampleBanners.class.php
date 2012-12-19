<?php
/**
 *   @copyright Copyright (c) 2007 Quality Unit s.r.o.
 *   @author Andrej Harsani
 *   @package PostAffiliatePro
 *   @since Version 4.0.0
 *   $Id: UpdateManager.class.php 18026 2008-05-14 08:07:20Z mbebjak $
 *
 *   Licensed under the Quality Unit, s.r.o. Dual License Agreement,
 *   Version 1.0 (the "License"); you may not use this file except in compliance
 *   with the License. You may obtain a copy of the License at
 *   http://www.qualityunit.com/licenses/gpf
 *
 */

/**
 * @package PostAffiliatePro
 */
class Pap_Install_SampleBanners extends Gpf_Object {

    /**
     * @var Gpf_Db_Account
     */
    protected $account;
    
    public function __construct(Gpf_Db_Account $account) {
        $this->account = $account;
    }
    
    public function createSampleBanners($campaignId) {
        $banner = new Pap_Common_Banner();
        $banner->setId('11110001');
        $banner->setName('Sample image banner 1');
        $banner->setBannerType(Pap_Common_Banner_Factory::BannerTypeImage);
        $banner->setCampaignId($campaignId);
        $banner->setStatus('A');
        $banner->setDestinationUrl("http://www.qualityunit.com");
        $banner->setSize("P468x60");
        $banner->setData1($this->copyToBannerUploads('sample_image_banner.gif'));
        $banner->setAccountId($this->account->getId());
        $banner->set("dateinserted", Gpf_Common_DateUtils::now());
        $banner->setWrapperId('plain');
        $banner->save();

        $banner = new Pap_Common_Banner();
        $banner->setId('11110002');
        $banner->setName('Sample text link 1');
        $banner->setBannerType(Pap_Common_Banner_Factory::BannerTypeText);
        $banner->setCampaignId($campaignId);
        $banner->setStatus('A');
        $banner->setDestinationUrl("http://www.qualityunit.com");
        $banner->setSize("U");
        $banner->setData1("Click here");
        $banner->setData2("to find out more about this link");
        $banner->set("dateinserted", Gpf_Common_DateUtils::now());
        $banner->setAccountId($this->account->getId());
        $banner->setWrapperId('plain');
        $banner->save();

        $banner = new Pap_Common_Banner();
        $banner->setId('11110003');
        $banner->setName('Sample Flash banner');
        $banner->setBannerType(Pap_Common_Banner_Factory::BannerTypeFlash);
        $banner->setCampaignId($campaignId);
        $banner->setStatus('A');
        $banner->setDestinationUrl("http://www.qualityunit.com");
        $banner->setSize("P468x60");
        $banner->setData1($this->copyToBannerUploads("sample_flash_banner.swf"));
        $banner->setData2("Opaque");
        $banner->set("dateinserted", Gpf_Common_DateUtils::now());
        $banner->setAccountId($this->account->getId());
        $banner->setWrapperId('plain');
        $banner->save();

        $banner = new Pap_Common_Banner();
        $banner->setId('11110004');
        $banner->setName('Sample HTML banner 1');
        $banner->setBannerType(Pap_Common_Banner_Factory::BannerTypeHtml);
        $banner->setCampaignId($campaignId);
        $banner->setStatus('A');
        $banner->setDestinationUrl("http://www.qualityunit.com");
        $banner->setSize("U");
        $banner->setData1('N');
        $banner->setData2('<table width="100%" border="0" cellpadding="3">
            <tr>
              <td align="left" valign="top"><img src="' . $this->copyToBannerUploads('sample_html_banner_image.gif') . '" alt=""/></td>
              <td></td>
              <td align="left" valign="top">
                <b>Post Affiliate Pro</b><br/>

                - a powerful affiliate management system that allows you to:<br/>
                - easy set up and maintain your own affiliate program  <br/>
                - pay your affiliates per lead per click per sale or %commission.<br/>
                - multi-tier commissions: up to 10 tiers<br/>
                - get more traffic for you website without additional costs<br/>
                - increase sales<br/>
                - already used by more than thousand merchants worldwide
                <br/>
              </td>
              </tr>
            <tr>
              <td colspan="3" align="left">
              Post Affiliate Pro offers you a vast spectrum of features and PRICE / FEATURES RATIO IS THE BEST you can find.
              <br/>You also get FREE INSTALLATION, lifetime upgrades and fast and helpful support.<br/><br/>
              <b>Post Affiliate Pro</b> is compatible with nearly all merchant accounts, payment gateways, shopping carts and
              membership systems.
              <br/>
              <a style="color:red;" href="{$targeturl}">Click here to learn more</a>
              </td>
            </tr>
           </table>');
        $banner->set("dateinserted", Gpf_Common_DateUtils::now());
        $banner->setAccountId($this->account->getId());
        $banner->setWrapperId('plain');
        $banner->save();

        $banner = new Pap_Common_Banner();
        $banner->setId('11110005');
        $banner->setName('Sample promotional email');
        $banner->setBannerType(Pap_Common_Banner_Factory::BannerTypePromoEmail);
        $banner->setCampaignId($campaignId);
        $banner->setStatus('A');
        $banner->setDestinationUrl("http://www.qualityunit.com");
        $banner->setSize("U");
        $banner->setData1("New generation affiliate software");
        $banner->setData2('Dear friend,<br><br>I would like to let you know about an affiliate software I recently found.<br>It is called Post Affiliate Pro.<br><br>Go to the link below learn more:<br><a href=\"{$targeturl}\">Post Affiliate Pro</a><br><br>best regards,<br><br>{$firstname} {$lastname}');
        $banner->set("dateinserted", Gpf_Common_DateUtils::now());
        $banner->setAccountId($this->account->getId());
        $banner->setWrapperId('plain');
        $banner->save();

        $banner = new Pap_Common_Banner();
        $banner->setId('11110006');
        $banner->setName('Sample Simple PDF book 1');
        $banner->setBannerType(Pap_Common_Banner_Factory::BannerTypePdf);
        $banner->setCampaignId($campaignId);
        $banner->setStatus('A');
        $banner->setDestinationUrl("http://www.qualityunit.com");
        $banner->setSize("U");
        $banner->setData1("book1.pdf");
        $banner->setData2('<span style="font-weight: bold;">Example Simple PDF book</span><br><br><img src="http://www.qualityunit.com/themes/site_themes/qu/pap/post_affiliate_pro_logo.gif"><br><br>Dear {$firstname} {$lastname},<br><br>let me present you this book.<br><br><ol><li>list 1</li><li>list 2</li><li>list 3</li></ol>To find out more, click <a href="{$targeturl}">here</a>.<br>');
        $banner->setData3('Sample SimplePDF book');
        $banner->set("dateinserted", Gpf_Common_DateUtils::now());
        $banner->setAccountId($this->account->getId());
        $banner->setWrapperId('plain');
        $banner->save();
    }
    
    private function copyToBannerUploads($fileName) {
        $source = new Gpf_Io_File(Gpf_Paths::getInstance()->getResourcePath($fileName, Gpf_Paths::IMAGE_DIR));
        $targetRelativePath = Gpf_Paths::getInstance()->getAccountDirectoryRelativePath() .
        Pap_Merchants_Banner_BannerUpload::BANNERS_DIR .
        $fileName;
        $target = new Gpf_Io_File('../' . $targetRelativePath);
        try {
            Gpf_Io_File::copy($source, $target, 0777);
        } catch (Exception $e) {
            throw new Gpf_Exception($this->_('Error during copy of sample banner image %s.', $source->getFileName()));
        }
        return Gpf_Paths::getInstance()->getFullBaseServerUrl() . $targetRelativePath;
    }
}
?>
