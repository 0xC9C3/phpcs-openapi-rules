# phpcs-openapi-rules

simple rules for openapi phpdoc formatting

dont forget to add 

```
        <rule ref="Squiz.Commenting.DocCommentAlignment">
          <exclude name="Squiz.Commenting.DocCommentAlignment.SpaceAfterStar" />
          <exclude name="Squiz.Commenting.DocCommentAlignment.NoSpaceAfterStar" />
        </rule>
``` 

to your phpcs.xml
