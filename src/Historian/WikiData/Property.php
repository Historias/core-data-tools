<?php
namespace Historian\Importer\WikiData;

use Eloquent\Enumeration\AbstractEnumeration;

final class Property extends AbstractEnumeration
{
    const INSTANCE_OF = 31;
    const CAPITAL = 36;
    const HAS_PART = 527;
    const ISO_2_LETTER_CODE = 297;
    const ISO_3_LETTER_CODE = 298;
    const ISO_NUMERIC_CODE = 299;

    const DISSOLVED_OR_ABOLISHED = 576;
    const INCEPTION = 571;
    const FOLLOWS = 155;
    const FOLLOWED_BY = 156;

    const START_TIME = 580;
    const END_TIME = 582;
}
