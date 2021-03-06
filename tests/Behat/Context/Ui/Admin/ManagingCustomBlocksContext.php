<?php

namespace Tests\Lakion\SyliusCmsBundle\Behat\Context\Ui\Admin;

use Behat\Behat\Context\Context;
use Tests\Lakion\SyliusCmsBundle\Behat\Page\Admin\CustomBlock\ShowPageInterface;
use Sylius\Behat\Page\Admin\Crud\IndexPageInterface;
use Tests\Lakion\SyliusCmsBundle\Behat\Page\Admin\CustomBlock\CreatePageInterface;
use Tests\Lakion\SyliusCmsBundle\Behat\Page\Admin\CustomBlock\UpdatePageInterface;
use Lakion\SyliusCmsBundle\Document\CustomBlock;
use Webmozart\Assert\Assert;

final class ManagingCustomBlocksContext implements Context
{
    /**
     * @var IndexPageInterface
     */
    private $indexPage;

    /**
     * @var CreatePageInterface
     */
    private $createPage;

    /**
     * @var UpdatePageInterface
     */
    private $updatePage;

    /**
     * @var ShowPageInterface
     */
    private $showPage;

    /**
     * @param IndexPageInterface $indexPage
     * @param CreatePageInterface $createPage
     * @param UpdatePageInterface $updatePage
     * @param ShowPageInterface $showPage
     */
    public function __construct(
        IndexPageInterface $indexPage,
        CreatePageInterface $createPage,
        UpdatePageInterface $updatePage,
        ShowPageInterface $showPage
    ) {
        $this->indexPage = $indexPage;
        $this->createPage = $createPage;
        $this->updatePage = $updatePage;
        $this->showPage = $showPage;
    }

    /**
     * @Given I want to create a new custom block
     * @Given I want to add a new custom block
     */
    public function iWantToCreateNewCustomBlock()
    {
        $this->createPage->open();
    }

    /**
     * @Given I browse custom blocks of the store
     */
    public function iWantToBrowseCustomBlocksOfTheStore()
    {
        $this->indexPage->open();
    }

    /**
     * @Given I have created a custom block :name with image :imagePath
     */
    public function iHaveCreatedCustomBlockWithImage($name, $imagePath)
    {
        $this->iWantToCreateNewCustomBlock();
        $this->iSetItsNameTo($name);
        $this->iAttachAsItsImage($imagePath);
        $this->iAddIt();
    }

    /**
     * @When I set its body to :body
     */
    public function iSetItsBodyTo($body)
    {
        $this->createPage->setBody($body);
    }

    /**
     * @When I set its link to :link
     */
    public function iSetItsLinkTo($link)
    {
        $this->createPage->setLink($link);
    }

    /**
     * @When I set its name to :name
     */
    public function iSetItsNameTo($name)
    {
        $this->createPage->setName($name);
    }

    /**
     * @When I set its title to :title
     */
    public function iSetItsTitleTo($title)
    {
        $this->createPage->setTitle($title);
    }

    /**
     * @When I attach :imagePath as its image
     */
    public function iAttachAsItsImage($imagePath)
    {
        $this->createPage->attachImage($imagePath);
    }

    /**
     * @When I add it
     * @When I try to add it
     */
    public function iAddIt()
    {
        $this->createPage->create();
    }

    /**
     * @Then /^I should be notified that (body|name) is required$/
     */
    public function iShouldBeNotifiedThatElementIsRequired($element)
    {
        Assert::same(
            $this->createPage->getValidationMessage($element),
            'This value should not be blank.'
        );
    }

    /**
     * @Then the custom block :name should appear in the store
     * @Then I should see the custom block :name in the list
     */
    public function theCustomBlockShouldAppearInTheStore($name)
    {
        if (!$this->indexPage->isOpen()) {
            $this->indexPage->open();
        }

        Assert::true($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    /**
     * @Then I should see :amount custom blocks in the list
     */
    public function iShouldSeeThatManyCustomBlocksInTheList($amount)
    {
        Assert::same(
            (int) $amount,
            $this->indexPage->countItems(),
            'Amount of custom blocks should be equal %s, but was %2$s.'
        );
    }

    /**
     * @Then the custom block :name should not be added
     */
    public function theCustomBlockShouldNotBeAdded($name)
    {
        if (!$this->indexPage->isOpen()) {
            $this->indexPage->open();
        }

        Assert::false($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }

    /**
     * @When /^I preview (this custom block)$/
     * @When I preview custom block :customBlock
     */
    public function iPreviewCustomBlock(CustomBlock $customBlock)
    {
        $this->showPage->open(['id' => $customBlock->getId()]);
    }

    /**
     * @Given /^I want to edit (this custom block)$/
     */
    public function iWantToEditThisCustomBlock(CustomBlock $customBlock)
    {
        $this->updatePage->open(['id' => $customBlock->getId()]);
    }

    /**
     * @When I change its body to :body
     */
    public function iChangeItsBodyTo($body)
    {
        $this->updatePage->changeBodyTo($body);
    }

    /**
     * @When I change its link to :link
     */
    public function iChangeItsLinkTo($link)
    {
        $this->updatePage->changeLinkTo($link);
    }

    /**
     * @When I change its title to :title
     */
    public function iChangeItsTitleTo($title)
    {
        $this->updatePage->changeTitleTo($title);
    }

    /**
     * @When I save my changes
     * @When I try to save my changes
     */
    public function iSaveMyChanges()
    {
        $this->updatePage->saveChanges();
    }

    /**
     * @When I delete custom block :name
     */
    public function iDeleteCustomBlock($name)
    {
        $this->indexPage->open();
        $this->indexPage->deleteResourceOnPage(['name' => $name]);
    }

    /**
     * @Then I should see :expected in this block contents
     */
    public function iShouldSeeInThisBlockContents($expected)
    {
        Assert::contains($this->showPage->getBlockContents(), $expected);
    }

    /**
     * @Then I should see an image
     */
    public function iShouldSeeImage()
    {
        // TODO: No other way to be sure that image was uploaded properly without messing with setting up the server
        Assert::notEmpty($this->showPage->getBlockImageUrl());
    }

    /**
     * @Then /^(this custom block) should have body "([^"]+)"$/
     */
    public function thisCustomBlockShouldHaveBody(CustomBlock $customBlock, $body)
    {
        $this->updatePage->open(['id' => $customBlock->getId()]);

        Assert::same($this->updatePage->getBody(), $body);
    }

    /**
     * @Then /^(this custom block) should have link "([^"]+)"$/
     */
    public function thisCustomBlockShouldHaveLink(CustomBlock $customBlock, $link)
    {
        $this->updatePage->open(['id' => $customBlock->getId()]);

        Assert::same($this->updatePage->getLink(), $link);
    }

    /**
     * @Then /^(this custom block) should have title "([^"]+)"$/
     */
    public function thisCustomBlockShouldHaveTitle(CustomBlock $customBlock, $title)
    {
        $this->updatePage->open(['id' => $customBlock->getId()]);

        Assert::same($this->updatePage->getTitle(), $title);
    }

    /**
     * @Then the custom block :name should no longer exist in the store
     */
    public function theCustomBlockShouldNoLongerExistInTheStore($name)
    {
        Assert::false($this->indexPage->isSingleResourceOnPage(['name' => $name]));
    }
}
