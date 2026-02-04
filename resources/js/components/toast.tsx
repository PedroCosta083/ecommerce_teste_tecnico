import { useEffect, useState } from 'react';
import { usePage } from '@inertiajs/react';
import { CheckCircle, XCircle, AlertCircle, Info, X } from 'lucide-react';

interface FlashMessage {
  success?: string;
  error?: string;
  warning?: string;
  info?: string;
}

export default function Toast() {
  const { props } = usePage();
  const flash = props.flash as FlashMessage;
  const [visible, setVisible] = useState(false);
  const [message, setMessage] = useState('');
  const [type, setType] = useState<'success' | 'error' | 'warning' | 'info'>('success');

  useEffect(() => {
    if (flash?.success) {
      setMessage(flash.success);
      setType('success');
      setVisible(true);
    } else if (flash?.error) {
      setMessage(flash.error);
      setType('error');
      setVisible(true);
    } else if (flash?.warning) {
      setMessage(flash.warning);
      setType('warning');
      setVisible(true);
    } else if (flash?.info) {
      setMessage(flash.info);
      setType('info');
      setVisible(true);
    }
  }, [flash]);

  useEffect(() => {
    if (visible) {
      const timer = setTimeout(() => {
        setVisible(false);
      }, 5000);
      return () => clearTimeout(timer);
    }
  }, [visible]);

  if (!visible) return null;

  const getIcon = () => {
    switch (type) {
      case 'success':
        return <CheckCircle className="h-5 w-5 text-green-600" />;
      case 'error':
        return <XCircle className="h-5 w-5 text-red-600" />;
      case 'warning':
        return <AlertCircle className="h-5 w-5 text-yellow-600" />;
      case 'info':
        return <Info className="h-5 w-5 text-blue-600" />;
    }
  };

  const getStyles = () => {
    switch (type) {
      case 'success':
        return 'bg-green-50 border-green-200 text-green-800';
      case 'error':
        return 'bg-red-50 border-red-200 text-red-800';
      case 'warning':
        return 'bg-yellow-50 border-yellow-200 text-yellow-800';
      case 'info':
        return 'bg-blue-50 border-blue-200 text-blue-800';
    }
  };

  return (
    <div className="fixed top-4 right-4 z-50 max-w-sm animate-in slide-in-from-right duration-300">
      <div className={`${getStyles()} border rounded-lg p-4 shadow-lg`}>
        <div className="flex items-start gap-3">
          {getIcon()}
          <div className="flex-1">
            <p className="font-medium text-sm">{message}</p>
          </div>
          <button
            onClick={() => setVisible(false)}
            className="text-gray-400 hover:text-gray-600 transition-colors"
          >
            <X className="h-4 w-4" />
          </button>
        </div>
      </div>
    </div>
  );
}