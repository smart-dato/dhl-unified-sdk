<?php

namespace SmartDato\DhlUnified\Enums;

enum LocationType: string
{
    case ServicePoint = 'servicepoint';
    case PostOffice = 'postoffice';
    case Postbank = 'postbank';
    case Locker = 'locker';
    case PoBox = 'pobox';
    case PostBox = 'postbox';
}
